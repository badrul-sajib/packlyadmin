<?php

namespace App\Enums;

enum WarrantyRecurringTypes: int
{
    case DAY = 1;
    case MONTH = 2;
    case YEAR = 3;

    public static function label($value): string
    {
        return match ((int) $value) {
            self::DAY->value => 'Day',
            self::MONTH->value => 'Month',
            self::YEAR->value => 'Year',
            default => 'Unknown',
        };
    }
}
