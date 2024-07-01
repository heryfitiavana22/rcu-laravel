<?php

namespace Heryfitiavana\RCU\Services;

use Illuminate\Support\Facades\Storage;
use Heryfitiavana\RCU\Contracts\RCUServiceInterface;

class RCUService implements RCUServiceInterface
{
    public function __construct(private $config)
    {
    }

    public function uploadStatus($query)
    {
        $store = $this->config["store"];
        $uploadInfo = $store->getItem($query["fileId"]);

        if (!$uploadInfo) {
            $newUpload = $store->createItem($query["fileId"], $query["chunkCount"]);
            return ['lastChunk' => $newUpload['lastUploadedChunkNumber']];
        }

        if ($uploadInfo['chunkCount'] != $query["chunkCount"]) {
            $store->removeItem($query["fileId"]);
            $newUpload = $store->createItem($query["fileId"], $query["chunkCount"]);
            return ['lastChunk' => $newUpload['lastUploadedChunkNumber']];
        }

        return ['lastChunk' => $uploadInfo['lastUploadedChunkNumber']];
    }

    public function upload($dto)
    {
        $store = $this->config["store"];
        $uploadInfo = $store->getItem($dto["fileId"]);

        if (!$uploadInfo) {
            throw new \Exception("Invalid upload info {$dto["fileId"]}");
        }

        $chunkId = "{$dto["chunkNumber"]}-{$dto["fileId"]}";
        Storage::put("{$this->config["tmpDir"]}/{$chunkId}", $dto["file"]);

        $uploadInfo = $store->updateItem($dto["fileId"], [
            'chunkFilenames' => array_merge($uploadInfo['chunkFilenames'], [$chunkId]),
            'lastUploadedChunkNumber' => $dto["chunkNumber"]
        ]);

        if ($uploadInfo['chunkCount'] > $dto["chunkNumber"]) {
            return ['message' => 'Chunk uploaded'];
        }

        $outputFile = "{$this->config["outputDir"]}/{$dto["originalFilename"]}";
        if (!Storage::exists($this->config["outputDir"])) {
            Storage::makeDirectory($this->config["outputDir"]);
        }
        $combinedFile = fopen(Storage::path($outputFile), 'w');

        foreach ($uploadInfo['chunkFilenames'] as $chunk) {
            $chunkPath = "{$this->config["tmpDir"]}/{$chunk}";
            if (!Storage::exists($chunkPath)) {
                fclose($combinedFile);
                Storage::delete($outputFile);
                $store->removeItem($dto["fileId"]);
                throw new \Exception('File corrupted');
            }

            fwrite($combinedFile, Storage::get($chunkPath));
            Storage::deleteDirectory($chunkPath);
        }

        fclose($combinedFile);
        $store->removeItem($dto["fileId"]);

        if ($this->config["onCompleted"]) {
            call_user_func($this->config["onCompleted"], ['outputFile' => $outputFile, 'fileId' => $dto["fileId"]]);
        }

        return ['message' => 'Upload complete', 'outputFile' => $outputFile];
    }
}
