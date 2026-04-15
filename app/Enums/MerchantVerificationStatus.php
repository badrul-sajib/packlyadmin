<?php

namespace App\Enums;

enum MerchantVerificationStatus: int
{
    case NOT_VERIFIED = 0;
    case PENDING      = 1;
    case PROCESSING   = 2;
    case VERIFIED     = 3;
    case REJECTED     = 4;

    public function label(): string
    {
        return match ($this) {
            self::NOT_VERIFIED => 'Not Verified',
            self::PENDING      => 'Pending',
            self::PROCESSING   => 'Processing',
            self::VERIFIED     => 'Verified',
            self::REJECTED     => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NOT_VERIFIED => 'bg-warning',
            self::PENDING      => 'bg-warning',
            self::PROCESSING   => 'bg-info',
            self::VERIFIED     => 'bg-success',
            self::REJECTED     => 'bg-danger',
        };
    }

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
