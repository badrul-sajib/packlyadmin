<?php

namespace App\Enums;

enum CancelBy: int
{
    case CUSTOMER = 1;
    case MERCHANT = 2;
    case ADMIN = 3;

    public function label(): string
    {
        return match ($this) {
            self::CUSTOMER => 'Customer',
            self::MERCHANT => 'Merchant',
            self::ADMIN => 'Admin',
        };
    }
}
