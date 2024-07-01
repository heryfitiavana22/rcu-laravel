<?php

namespace Tests\Unit;

use Tests\TestCase;
use Heryfitiavana\RCU\StoreProviders\JsonStoreProvider;

class JsonStoreProviderTest extends TestCase
{
    public function testCreateItem()
    {
        $filePath = 'uploads/test.json';
        $provider = new JsonStoreProvider($filePath);

        $provider->createItem('file1', 4);

        $item = $provider->getItem('file1');
        $this->assertNotNull($item);
        $this->assertEquals('file1', $item['id']);
        $this->assertEquals(4, $item['chunkCount']);
        $this->assertEquals(0, $item['lastUploadedChunkNumber']);
        $this->assertEquals([], $item['chunkFilenames']);
    }

    public function testUpdateItem()
    {
        $filePath = 'uploads/test.json';
        $provider = new JsonStoreProvider($filePath);

        $provider->createItem('file1', 4);
        $provider->updateItem('file1', ['lastUploadedChunkNumber' => 2, 'chunkFilenames' => ['chunk1', 'chunk2']]);

        $item = $provider->getItem('file1');
        $this->assertNotNull($item);
        $this->assertEquals(2, $item['lastUploadedChunkNumber']);
        $this->assertEquals(['chunk1', 'chunk2'], $item['chunkFilenames']);
    }

    public function testRemoveItem()
    {
        $filePath = 'uploads/test.json';
        $provider = new JsonStoreProvider($filePath);

        $provider->createItem('file1', 4);
        $provider->removeItem('file1');

        $item = $provider->getItem('file1');
        $this->assertNull($item);
    }

    public function testGetItemNotExist()
    {
        $filePath = 'uploads/test.json';
        $provider = new JsonStoreProvider($filePath);

        $item = $provider->getItem('file1');
        $this->assertNull($item);
    }

    public function testCreateItemAlreadyExists()
    {
        $this->expectException(\Exception::class);

        $filePath = 'uploads/test.json';
        $provider = new JsonStoreProvider($filePath);

        $provider->createItem('file1', 4);
        $provider->createItem('file1', 4);
    }
}
