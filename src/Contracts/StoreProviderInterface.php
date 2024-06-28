<?php

namespace Heryfitiavana\RCU\Contracts;

interface StoreProviderInterface
{
    public function getItem(String $id);
    public function createItem(String $id, Int $chunkCount);
    public function updateItem(String $id, $update);
    public function removeItem(String $id);
}
