<?php

namespace Viandwi24\LaravelExtension\Facades;

class Menu extends \Illuminate\Support\Facades\Facade
{
    public static function getFacadeAccessor()
    {
        return 'menu';
    }
}