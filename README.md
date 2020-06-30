# Laravel Extension
Plugin, Extension and Module System For Laravel. Inspirate from Wordpress Plugins.


Table of contents
=================

<!--ts-->
   * [Installing](#installing)
   * [Extension Folder Structure](#extension-folder-structure)
   * [Create New Extension](#create-new-extension)
      * [with Artisan Command](#with-artisan-command)
      * [Manual](#manual)
   * [Hook](#hook)
      * [Action](#action)
      * [Filter](#filter)
<!--te-->

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


###  Artisan Console Command Support
Make new extension easy :
```
php artisan extension:new ExampleExtension
```
Update list installed extension :
```
php artisan extension:update-list
```
Get list installed extension :
```
php artisan extension:list
```
Enable a extension : (plugin must be added in list first)
```
php artisan extension:enable ExamplePlugin
```
Disable a extension :
```
php artisan extension:disable ExamplePlugin
```
Inspect a Extension
```
php artisan extension:inspect ExamplePlugin
```
Get Hook list (all)
```
php artisan hook:list
```
Get Hook list specific type (action or filter)
```
# only action :
php artisan hook:list --action

# only filter :
php artisan hook:list --filter
```


## Create New Extension 
### with Artisan Command
you can create new extension with this command :
```
php artisan extension:new ExampleTes
```
after success, you can refresh extension list with :
```
php artisan extension:update-list
```
and you can check if updating list success :
```
php artisan extension:list
```
for enable your extension, use :
```
php artisan extension:enable ExampleTes
```
### Manual
| note : this use indonesian language, you can translate this.
Lokasi extension harus ada pada folder utama extension, secara default 
ada pada `app\Extension`. berikut panduan singkat cara pembuatan extension :
* membuat folder utama extension, misal `app\Extension\MyPlugin`
* buat file config untuk plugin anda di `app\Extension\MyPlugin\extension.json`, isi dengan :
```
{
    "name": "My Example Plugin",
    "description": "describe your extension.",
    "version": "1.0.0",
    "provider": "MyPluginServiceProvider",
    "author": {
        "name": "viandwi24",
        "site": "https://github.com/viandwi24"
    }
}
```
* Buat service provider untuk extension anda, di folder working extension tadi, sebagai contoh `app\Extension\MyPlugin\MyPluginServiceProvider.php` isinya :
```
<?php

namespace Extension\MyPlugin;

use Viandwi24\LaravelExtension\Support\ServiceProvider;

class MyPluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
```
* Sekarang, jalankan perintah ini diartisan untuk mengupdate list extension yang terinstall di `app\Extension\extension.json` :
```
php artisan extension:update-list
```
anda bisa menambahkanya otomatis di file config `app\Extension\extension.json`, jika anda menjalankan perintah diatas, perintah tersebut akan otomatis mengupdate daftar extension anda yang ada di `app\Extension\extension.json`, seharusnya menjadi seperti berikut nantinya file `app\Extension\extension.json` :
```
{
    "list": [
        "MyPlugin"
    ],
    "active": []
}
```
* Cek apakah extension tersebut sudah terdaftar dengan menjalankan perintah :
```
php artisan extension:list
```
* jika terdaftar tapi statusnya belum `active`, silahkan jalankan perintah :
```
php artisan extension:enable MyPlugin
```


## Hook
### Action
with Hook Action, you can make multi-action system and management action.  
Make Action :
```
Hook::addAction(string $extension, string $name, \Closure $callback, int $priority = 10)
```
Run Action :
```
Hook::runAction(string $name)
```
  
For Example, you can make action hook name "save_post", and this action can make many action callback
```
use Viandwi24\LaravelExtension\Facades\Hook;
use App\Models\Post;

Hook::addAction('extension_name', 'save_post', function ($title) {
    $create = Post::create([ 'title' => $title ]);
}, 15);

Hook::addAction('another_extension', 'save_post', function ($title) {
    Log::create("Creating post with title {$title}");
}, 10);
```
  
And You Can Run :
```
use Viandwi24\LaravelExtension\Facades\Hook;

$title = "Example Post";
Hook::runAction('save_post', $title);
```
### Filter
Filter make data can modification by multiple closure, this a helpfull for make Extension.
Make Filter :
```
Hook::addFilter(string $extension, string $name, \Closure $callback, int $priority = 10)
```
Apply Action :
```
$result = Hook::applyFilter(string $name, $value, ...$params)
```
  
For example, i make 2 function for modification a data `$title` :
```
use Viandwi24\LaravelExtension\Facades\Hook;

Hook::addFilter('extension_name', 'save_post_params', function ($title) {
    return "a {$title}.";
}, 15);

Hook::addFilter('another_extension', 'save_post_params', function ($title) {
    return strtolower($title);
}, 10);


$title = "Example Post";
$result = Hook::applyFilter('save_post_params', $title);
// result output : a example post.
```
