<?php

namespace App\Enums;

enum MerchantStatus: int
{
    case Pending   = 0;
    case Active    = 1;
    case Inactive  = 2;
    case Suspended = 3;
    case Rejected  = 4;

    public static function label($value): string
    {
        return match ($value) {
            self::Pending   => 'Pending',
            self::Active    => 'Active',
            self::Inactive  => 'Inactive',
            self::Suspended => 'Suspended',
            self::Rejected  => 'Rejected',
            default         => 'Unknown',
        };
    }

    public static function toArray(): array
    {
        return [
            'Pending'   => self::Pending,
            'Active'    => self::Active,
            'Inactive'  => self::Inactive,
            'Suspended' => self::Suspended,
            'Rejected'  => self::Rejected,
        ];
    }

    public static function color($value): string
    {
        return match ($value) {
            self::Pending   => 'warning',
            self::Active    => 'success',
            self::Inactive  => 'secondary',
            self::Suspended => 'danger',
            self::Rejected  => 'danger',
            default         => 'info',
        };
    }
}
