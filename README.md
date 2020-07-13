# Patches for Laravel 5

## Install

Via Composer

``` bash
$ composer require webmachine/patches
```

Next, you must install the service provider:

```php
// config/app.php
'providers' => [
    ...
    Webmachine\Patches\PatchesServiceProvider::class,
];
```

Publish

``` bash
$ php artisan vendor:publish --provider="Webmachine\Patches\PatchesServiceProvider"
```

## Usage

Create a new Patch
``` bash
$ php artisan make:patch MyPatch
```
Run Patch
``` bash
$ php artisan patch MyPatch
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
