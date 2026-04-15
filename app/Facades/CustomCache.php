<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CustomCache extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'customCache';
    }
}
