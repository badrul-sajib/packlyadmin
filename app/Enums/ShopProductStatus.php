<?php

namespace App\Enums;

enum ShopProductStatus: int
{
    case PENDING   = 1;
    case APPROVED  = 2;
    case REJECTED  = 3;
    case DISSABLED = 4;

    public static function label(): array
    {
        return [
            self::PENDING->value   => 'Pending',
            self::APPROVED->value  => 'Approved',
            self::REJECTED->value  => 'Rejected',
            self::DISSABLED->value => 'Dissabled',
        ];
    }

    public static function status_by_color(): array
    {
        return [
            self::PENDING->value   => 'alert-warning',
            self::APPROVED->value  => 'alert-success',
            self::REJECTED->value  => 'alert-danger',
            self::DISSABLED->value => 'alert-info',
        ];
    }
}
