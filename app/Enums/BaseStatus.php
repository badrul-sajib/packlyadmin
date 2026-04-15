<?php

namespace App\Enums;

enum BaseStatus: int
{
    case ACTIVE   = 1;
    case INACTIVE = 2;
    case FIXED    = 3;

    public static function all(): array
    {
        return [
            self::ACTIVE->value   => 'Active',
            self::INACTIVE->value => 'InActive',
            self::FIXED->value    => 'Fixed',
        ];
    }
}
