<?php

namespace Viandwi24\LaravelExtension\Facades;

class Extension extends \Illuminate\Support\Facades\Facade
{
    public static function getFacadeAccessor()
    {
        return 'extension';
    }
}