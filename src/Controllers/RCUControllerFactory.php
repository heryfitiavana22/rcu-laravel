<?php

namespace RCU\Controllers;

use RCU\Services\RCUService;
use RCU\StoreProviders\JsonStoreProvider;

class RCUControllerFactory
{
    public static function createController($config = null)
    {
        $defaultConfig = [
            "store" => new JsonStoreProvider('rcu/uploads.json'),
            "tmpDir" => "rcu/tmp",
            "outputDir" => "rcu/output",
            "onCompleted" => function ($data) {
            },
        ];

        $finalConfig = $config ?? $defaultConfig;
        $rcuService = new RCUService($finalConfig);

        return new UploadController($rcuService);
        
    }
}
