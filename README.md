# rcu-laravel
[Resumable chunk upload](https://github.com/heryfitiavana22/resumable-chunk-upload) for Laravel.

## Installation


```bash
  composer require heryfitiavana/rcu-laravel
```

## Usage

### Defining Routes
Add the following routes to your `routes/web.php` file:

```php
use Heryfitiavana\RCU\Controllers\UploadController;

Route::get('/uploadStatus', [UploadController::class, 'uploadStatus']);
Route::post('/upload', [UploadController::class, 'upload']);

```

### Custom configuration
You can customize the package's behavior by defining a custom configuration array. Here's an example:

```php
$customConfig = [
    "store" => new JsonStoreProvider('rcu/uploads.json'),
    "tmpDir" => "rcu/tmp",
    "outputDir" => "rcu/output",
    "onCompleted" => function ($data) {
    },
];
```
### Integrating the Custom Configuration
You can integrate the custom configuration in two ways:

#### Option 1: Directly in Routes `routes/web.php`

```php
use Heryfitiavana\RCU\Controllers\UploadController;
use Heryfitiavana\RCU\Controllers\RCUControllerFactory;

$RCUController = RCUControllerFactory::createController($customConfig);

Route::get('/uploadStatus', function () use ($RCUController) {
    return $RCUController->uploadStatus(request());
});

Route::post('/upload', function () use ($RCUController) {
    return $RCUController->upload(request());
});;

```

#### Option 2: Using a Service Provider
1. Create a new service provider file at `app/Providers/RCUServiceProvider`.

```php
<?php

namespace App\Providers;

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
            return RCUControllerFactory::createController($customConfig);
        });
        $this->app->bind(StoreProviderInterface::class, JsonStoreProvider::class);
        $this->app->bind(RCUServiceInterface::class, RCUService::class);
    }

    public function boot()
    {
    }
}
```

2. Register the service provider in `bootstrap/providers.php`
```php
<?php

return [
    // other providers
    App\Providers\RCUServiceProvider::class,
];
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
