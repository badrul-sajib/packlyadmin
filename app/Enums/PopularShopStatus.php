<?php

namespace App\Enums;

enum PopularShopStatus: int
{
    case ACTIVE   = 1;
    case INACTIVE = 0;

    public static function getLabels(): array
    {
        return [
            self::ACTIVE->value   => 'Active',
            self::INACTIVE->value => 'Inactive',
        ];
    }
}
