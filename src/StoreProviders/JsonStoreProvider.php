<?php

namespace Heryfitiavana\RCU\StoreProviders;

use Illuminate\Support\Facades\Storage;
use Heryfitiavana\RCU\Contracts\StoreProviderInterface;

class JsonStoreProvider implements StoreProviderInterface
{
    private $rows = [];
    private String $filePath;

    public function __construct(String $filePath)
    {
        $this->filePath = $filePath;

        if (Storage::exists($filePath)) {
            $content = Storage::get($filePath);
            $data = json_decode($content, true);

            if (isset($data['rows']) && is_array($data['rows'])) {
                $this->rows = $data['rows'];
            } else {
                $this->rows = [];
                Storage::put($filePath, json_encode(['rows' => []]));
            }
        } else {
            Storage::put($filePath, json_encode(['rows' => []]));
        }
    }

    public function getItem($id)
    {
        return collect($this->rows)->firstWhere('id', $id);
    }

    public function createItem($id, $chunkCount)
    {
        if (collect($this->rows)->firstWhere('id', $id)) {
            throw new \Exception("Upload already exists for {$id}");
        }

        $upload = [
            'id' => $id,
            'chunkCount' => $chunkCount,
            'lastUploadedChunkNumber' => 0,
            'chunkFilenames' => []
        ];

        $this->rows[] = $upload;
        $this->persist();

        return $this->getItem($id);
    }

    public function updateItem($id, $update)
    {
        $this->rows = collect($this->rows)->map(function ($el) use ($id, $update) {
            if ($el['id'] == $id) {
                return array_merge($el, $update);
            }
            return $el;
        })->all();

        $this->persist();

        return $this->getItem($id);
    }

    public function removeItem($id)
    {
        $this->rows = collect($this->rows)->reject(function ($el) use ($id) {
            return $el['id'] == $id;
        })->all();

        $this->persist();
    }

    private function persist()
    {
        Storage::put($this->filePath, json_encode(['rows' => $this->rows]));
    }
}
