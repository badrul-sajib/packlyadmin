<?php

namespace App\Enums;

enum DeliveryType: int
{
    case REGULAR = 1;
    case EXPRESS = 2;

    public static function getTypeLabels(): array
    {
        return [
            self::REGULAR->value => 'Regular',
            self::EXPRESS->value => 'Express',
        ];
    }

    public static function getPrice($type): int
    {
        return match ($type) {
            self::REGULAR->value => 0,
            self::EXPRESS->value => 50,
            default              => 0,
        };
    }
}
