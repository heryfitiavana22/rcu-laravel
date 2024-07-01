<?php

use Tests\TestCase;
use Heryfitiavana\RCU\Services\RCUService;
use Heryfitiavana\RCU\Contracts\StoreProviderInterface;
use Illuminate\Support\Facades\Storage;

class RCUServiceTest extends TestCase
{
    public function testUploadStatus()
    {
        $store = $this->createMock(StoreProviderInterface::class);
        $store->method('getItem')
            ->willReturn([
                'id' => 'file.txt',
                'chunkCount' => 4,
                'chunkFilenames' => ['1-chunk.txt', '2-chunk.txt'],
                'lastUploadedChunkNumber' => 2,
            ]);

        $service = new RCUService([
            'store' => $store,
            'tmpDir' => 'tmp',
            'outputDir' => 'output',
            "onCompleted" => function ($data) {
            },
        ]);

        $response = $service->uploadStatus(['fileId' => 'file.txt', 'chunkCount' => 4]);

        $this->assertEquals(['lastChunk' => 2], $response);
    }

    public function testUploadStatusWithNewFile()
    {
        $store = $this->createMock(StoreProviderInterface::class);
        $store->method('getItem')
            ->willReturn(null);
        $store->method('createItem')
            ->willReturn([
                'id' => 'file.txt',
                'chunkCount' => 4,
                'lastUploadedChunkNumber' => 0,
                'chunkFilenames' => [],
            ]);

        $service = new RCUService([
            'store' => $store,
            'tmpDir' => 'tmp',
            'outputDir' => 'output',
            "onCompleted" => function ($data) {
            },
        ]);

        $response = $service->uploadStatus(['fileId' => 'file.txt', 'chunkCount' => 4]);

        $this->assertEquals(['lastChunk' => 0], $response);
    }

    public function testUploadChunk()
    {
        $store = $this->createMock(StoreProviderInterface::class);
        $store->method('getItem')
            ->willReturn([
                'id' => 'file.txt',
                'chunkCount' => 4,
                'lastUploadedChunkNumber' => 0,
                'chunkFilenames' => [],
            ]);

        $store->method('updateItem')
            ->willReturn([
                'id' => 'file.txt',
                'chunkCount' => 4,
                'lastUploadedChunkNumber' => 1,
                'chunkFilenames' => ['1-file.txt'],
            ]);

        $service = new RCUService([
            'store' => $store,
            'tmpDir' => 'tmp',
            'outputDir' => 'output',
            "onCompleted" => function ($data) {
            },
        ]);

        $response = $service->upload([
            'file' => 'test data',
            'fileId' => 'file.txt',
            'chunkCount' => 4,
            'chunkNumber' => 1,
            'chunkSize' => 9,
            'fileSize' => 36,
            'originalFilename' => 'file.txt',
        ]);

        $this->assertEquals(['message' => 'Chunk uploaded'], $response);
    }

    public function testCompleteUpload()
    {
        $store = $this->createMock(StoreProviderInterface::class);
        $store->method('getItem')
            ->willReturn([
                'id' => 'file.txt',
                'chunkCount' => 2,
                'lastUploadedChunkNumber' => 1,
                'chunkFilenames' => ['1-file.txt'],
            ]);

        Storage::put('tmp/1-file.txt', 'test data 1');
        Storage::put('tmp/2-file.txt', 'test data 2');

        $store->method('updateItem')
            ->willReturn([
                'id' => 'file.txt',
                'chunkCount' => 2,
                'lastUploadedChunkNumber' => 2,
                'chunkFilenames' => ['1-file.txt', '2-file.txt'],
            ]);

        $service = new RCUService([
            'store' => $store,
            'tmpDir' => 'tmp',
            'outputDir' => 'output',
            "onCompleted" => function ($data) {
            },
        ]);

        $response = $service->upload([
            'file' => 'test data 2',
            'fileId' => 'file.txt',
            'chunkCount' => 2,
            'chunkNumber' => 2,
            'chunkSize' => 11,
            'fileSize' => 22,
            'originalFilename' => 'file.txt',
        ]);

        $this->assertEquals(['message' => 'Upload complete', 'outputFile' => 'output/file.txt'], $response);
    }

    public function testFileCorrupted()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File corrupted');

        $store = $this->createMock(StoreProviderInterface::class);
        $store->method('getItem')
            ->willReturn([
                'id' => 'file.txt',
                'chunkCount' => 2,
                'lastUploadedChunkNumber' => 1,
                'chunkFilenames' => ['1-file.txt', '2-file.txt', '3-file.txt'],
            ]);

        Storage::put('tmp/1-file.txt', 'test data 1');

        $store->method('updateItem')
            ->willReturn([
                'id' => 'file.txt',
                'chunkCount' => 2,
                'lastUploadedChunkNumber' => 2,
                'chunkFilenames' => ['1-file.txt', '2-file.txt', '3-file.txt'],
            ]);

        $service = new RCUService([
            'store' => $store,
            'tmpDir' => 'tmp',
            'outputDir' => 'output',
            "onCompleted" => function ($data) {
            },
        ]);

        $store->method('removeItem')
            ->willReturn(null);

        $response = $service->upload([
            'file' => 'test data 2',
            'fileId' => 'file.txt',
            'chunkCount' => 2,
            'chunkNumber' => 2,
            'chunkSize' => 11,
            'fileSize' => 22,
            'originalFilename' => 'file.txt',
        ]);
    }
}
