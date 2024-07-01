<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Heryfitiavana\RCU\Controllers\UploadController;
use Heryfitiavana\RCU\Services\RCUService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class UploadControllerTest extends TestCase
{
    public function testUploadStatus()
    {
        $rcuService = $this->createMock(RCUService::class);
        $rcuService->method('uploadStatus')
            ->with(['fileId' => 'file.txt', 'chunkCount' => 4])
            ->willReturn(['lastChunk' => 2]);

        $controller = new UploadController($rcuService);
        $request = new Request();
        $request->merge(['fileId' => 'file.txt', 'chunkCount' => 4]);

        $response = $controller->uploadStatus($request);

        $this->assertEquals(200, $response->status());
    }

    public function testUpload()
    {
        $rcuService = $this->createMock(RCUService::class);

        $rcuService->method('upload')
            ->with([
                'fileId' => 'file.txt',
                'chunkNumber' => 1,
                'originalFilename' => 'file.txt',
                'chunkCount' => 4,
                'chunkSize' => 9,
                'fileSize' => 36,
                'file' => $this->createMockUploadedFile(), // Add this line
            ])
            ->willReturn(['message' => 'Chunk uploaded']);

        $controller = new UploadController($rcuService);
        $request = new Request();
        $request->merge([
            'fileId' => 'file.txt',
            'chunkNumber' => 1,
            'originalFilename' => 'file.txt',
            'chunkCount' => 4,
            'chunkSize' => 9,
            'fileSize' => 36,
        ]);
        $request->files->set('file', $this->createMockUploadedFile());

        $response = $controller->upload($request);

        $this->assertEquals(200, $response->status());
    }

    public function testUploadValidationFails()
    {
        $this->expectException(\Exception::class);
        $rcuService = $this->createMock(RCUService::class);
        $controller = new UploadController($rcuService);
        $request = new Request();

        $response = $controller->upload($request);
    }

    private function createMockUploadedFile()
    {
        $fileContent = 'test data';
        $fileName = 'test.txt';
        $mimeType = 'text/plain';

        $tmpFile = tmpfile();
        fwrite($tmpFile, $fileContent);
        $filePath = stream_get_meta_data($tmpFile)['uri'];

        return new UploadedFile(
            $filePath, 
            $fileName,
            $mimeType,
            null,
            true
        );
    }
}
