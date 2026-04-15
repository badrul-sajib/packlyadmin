<?php

namespace App\Enums;

enum PaymentStatus: int
{
    case PENDING     = 1;
    case PAID        = 2;
    case UNKNOWN     = 3;
    case CANCELLED   = 4;
    case FAILED      = 5;
    case UNATTEMPTED = 6;
    case EXPIRED     = 7;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getStatusLabels(): array
    {
        return [
            self::PENDING->value     => 'Pending',
            self::PAID->value        => 'Paid',
            self::UNKNOWN->value     => 'Unknown',
            self::CANCELLED->value   => 'Cancelled',
            self::FAILED->value      => 'Failed',
            self::UNATTEMPTED->value => 'Unattempted',
            self::EXPIRED->value     => 'Expired',
        ];
    }

    public static function status_by_color(): array
    {
        return [
            self::PENDING->value     => 'warning',
            self::PAID->value        => 'success',
            self::UNKNOWN->value     => 'success',
            self::CANCELLED->value   => 'danger',
            self::FAILED->value      => 'warning',
            self::UNATTEMPTED->value => 'warning',
            self::EXPIRED->value     => 'danger',
        ];
    }
}
