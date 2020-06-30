# Laravel Extension
Plugin, Extension and Module System For Laravel. Inspirate from Wordpress Plugins.

## Installing
Install from composer :
```
composer require viandwi24/laravel-extension
```
We make this package with Auto Discovery, but you can add manual :
```
# service provider :
Viandwi24\LaravelExtension\LaravelExtensionServiceProvider::class

# aliases
"Extension" => Viandwi24\LaravelExtension\Facades\Extension::class,
"Hook" => Viandwi24\LaravelExtension\Facades\Hook::class
```
Publish Config :
```
php artisan vendor:publish --provider="Viandwi24\LaravelExtension\LaravelExtensionServiceProvider"
```
Added Namespace To Your Composer : (edit your laravel `composer.json`)
```
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Extension\\": "app/Extension/",
    },
},
```
And then, you can generate composer autoload :
```
composer dump-autoload
```
For first time after installing, you must run this command for generate extension folder in `app\Extension` :
```
php artisan extension:init
```


## Extension Folder Structure
By Default, Extension path in `app/Extension`, you can create Extension in this folder.
```
. app
├── Console
├── Exceptions
├── Extension
│   ├── ExampleExtension
│   │   └── extension.json
│   │   └── ServiceProvider.php
│   ├── MyExtension
│   │   ├── extension.json
│   │   └── MyExtensionServiceProvider.php
│   └── extension.json
├── Http
├── Providers
├── User.php
```