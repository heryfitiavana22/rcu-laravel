<?php

namespace Heryfitiavana\RCU;

use Illuminate\Support\ServiceProvider;
use Heryfitiavana\RCU\Contracts\RCUServiceInterface;
use Heryfitiavana\RCU\Contracts\StoreProviderInterface;
use Heryfitiavana\RCU\Controllers\RCUControllerFactory;
use Heryfitiavana\RCU\Controllers\UploadController;
use Heryfitiavana\RCU\Services\RCUService;
use Heryfitiavana\RCU\StoreProviders\JsonStoreProvider;

class RCUServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(UploadController::class, function ($app) {
            return RCUControllerFactory::createController();
        });
        $this->app->bind(StoreProviderInterface::class, JsonStoreProvider::class);
        $this->app->bind(RCUServiceInterface::class, RCUService::class);
    }

    public function boot()
    {
    }
}
