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
### Use the configuration
Go to `app/Providers/AppServiceProvider.php` and add the following code

```php

use Illuminate\Support\ServiceProvider;
use Heryfitiavana\RCU\Controllers\RCUControllerFactory;
use Heryfitiavana\RCU\Controllers\UploadController;
use Heryfitiavana\RCU\StoreProviders\JsonStoreProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // other ...
        $this->app->singleton(UploadController::class, function ($app) {
            $cocustomConfignfig = [
                "store" => new JsonStoreProvider('rcu/uploads.json'),
                "tmpDir" => "rcuc/tmp",
                "outputDir" => "rcuc/output",
                "onCompleted" => function ($data) {
                },
            ];
            return RCUControllerFactory::createController($config);
        });
    }

    public function boot()
    {
    }
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
