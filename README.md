# Laravel Extension
Plugin, Extension and Module System For Laravel. Inspirate from Wordpress Plugins.

![screenshot preview](https://raw.githubusercontent.com/viandwi24/laravel-extension/master/resources/screenshot.png)

# Table of contents
<!--ts-->
   * [Installing](#installing)
   * [Extension Management Page](#extension-management-page)
   * [Extension Folder Structure](#extension-folder-structure)
   * [Create New Extension](#create-new-extension)
      * [with Artisan Command](#with-artisan-command)
      * [Manual](#manual)
   * [Hook](#hook)
      * [Action](#action)
      * [Filter](#filter)
   * [Menu Builder](#menu-builder)
   * [Unit Test](#unit-test)
   * [Examples](#examples)
      * [Making Dynamic css and javascript in html blade](#Making-Dynamic-css-and-javascript-in-html-blade)
   * [License](#license)
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

## Extension Management Page
For a simple Extension management page, added this route to your rourtes `routes/web.php` :
```
use Viandwi24\LaravelExtension\Facades\Extension;

Extension::routes();
```
Save, and then access in your browser `http://mylaravelproject.test/extension` or 'localhost:8000/extension`, and finaly, you see this page :
![screenshot extension management page](https://raw.githubusercontent.com/viandwi24/laravel-extension/master/resources/screenshot1.png)

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


## Menu Builder
This package include a simple menu builder, for make dynamic menu in your project,  
this is a example :
```
use Viandwi24\LaravelExtension\Facades\Menu;

Menu::add('admin.navbar', 'Dashboard', url('/'));
Menu::add('admin.navbar', 'Extension', url('/extension'));
```
And then, for render to string html, use this. For example i am make `sidebar.blade.php` :
```
@php
$menu = Menu::render('admin.navbar', function ($title, $url) {
    return '<li class="nav-item">
        <a class="nav-link" href="'.$url.'">
            <i class="nav-icon la la-lg la-dashboard"></i>
            '.$title.'
        </a>
    </li>';
});
@endphp

<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            {!! $menu !!}
        </ul>
    </nav>
</div>
```



## Unit Test
You can running test with phpunit with this command :
```
composer test
```
or : (installed phpunit via composer)
```
vendor/bin/phpunit tests/HookActionTest
vendor/bin/phpunit tests/HookFilterTest
vendor/bin/phpunit tests/MenuBuilderTest
```

## Examples
### Making Dynamic css and javascript in html blade
For example, a make a page with dynamic component with Hook filter.
In `layouts/app.blade.php` :
```
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Example Page</title>
        
        @applyfilter("html__styless")
            <!-- dynamic styles with Hook Filter -->
        @applyfilter
    </head>
    <body class="app aside-menu-fixed sidebar-lg-show">
        <div id="app"></div>

        @applyfilter("html__scripts")
            <!-- dynamic scripts with Hook Filter -->
        @applyfilter
    </body>
</html>
```
And in controlle :
```
public function index()
{
    //  system filter
    Hook::addFilter("system", "html__styles", function ($old_value) {
        return $old_value . '<link href="bootstrap.min.css" rel="stylesheet">';
    }, 15);
    Hook::addFilter("system", "html__scripts", function ($old_value) {
        return $old_value .'<script src="bootstrap.min.js"><script>';
    }, 15);

    // another extension filter
    Hook::addFilter("example_extension", "html__scripts", function ($old_value) {
        return '<script src="jquery.min.js"><script>' . $old_value;
    }, 15);
    return view('layouts.app');
}
```
And then, final result html is :
```
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Example Page</title>
        
        <!-- dynamic styles with Hook Filter -->
        <link href="bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="app aside-menu-fixed sidebar-lg-show">
        <div id="app"></div>

        <!-- dynamic scripts with Hook Filter -->
        <script src="jquery.min.js"><script>
        <script src="bootstrap.min.js"><script>
    </body>
</html>
```



## License
The MIT License (MIT). Please see
<a href="https://github.com/viandwi24/laravel-extension/blob/master/LICENSE.md">License File</a>
for more information.