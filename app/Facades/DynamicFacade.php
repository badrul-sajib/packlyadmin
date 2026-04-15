<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DynamicFacade extends Facade
{
    protected static $serviceAccessor;

    /**
     * Dynamically set the service accessor.
     */
    public static function setFacadeAccessor(string $serviceAccessor): void
    {
        static::$serviceAccessor = $serviceAccessor;
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return static::$serviceAccessor;
    }
}
