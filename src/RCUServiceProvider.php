<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use RCU\Contracts\RCUServiceInterface;
use RCU\Contracts\StoreProviderInterface;
use RCU\Controllers\RCUControllerFactory;
use RCU\Controllers\UploadController;
use RCU\Services\RCUService;
use RCU\StoreProviders\JsonStoreProvider;

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
        // $this->publishes([
        //     __DIR__.'/../config/rcu.php' => config_path('rcu.php'),
        // ]);
    }
}
