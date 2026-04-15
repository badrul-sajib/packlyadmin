<?php

namespace App\Enums;

enum UserRole: int
{
    case SUPER_ADMIN      = 1;
    case ADMIN            = 2;
    case USER             = 3;
    case MERCHANT         = 4;
    case SHOP_ADMIN       = 5;

    public static function getLabels(): array
    {
        return [
            self::SUPER_ADMIN->value      => 'Super Admin',
            self::ADMIN->value            => 'Admin',
            self::USER->value             => 'User',
            self::MERCHANT->value         => 'Merchant',
            self::SHOP_ADMIN->value       => 'Shop Admin',
        ];
    }

    public function label(): string
    {
        return self::getLabels()[$this->value];
    }
}
