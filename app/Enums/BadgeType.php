<?php

namespace App\Enums;

enum BadgeType: int
{
    case GOLD            = 1;
    case SILVER          = 2;
    case BRONZE          = 3;
    case FREE_SHIPPING   = 4;
    case FREE_GIFT       = 5;
    case FREE_DISCOUNT   = 6;
    case FREE_BONUS      = 7;
    case FREE_EXPERIENCE = 8;

    public static function toArray(): array
    {
        return [
            self::GOLD->value            => 'Gold',
            self::SILVER->value          => 'Silver',
            self::BRONZE->value          => 'Bronze',
            self::FREE_SHIPPING->value   => 'Free Shipping',
            self::FREE_GIFT->value       => 'Free Gift',
            self::FREE_DISCOUNT->value   => 'Free Discount',
            self::FREE_BONUS->value      => 'Free Bonus',
            self::FREE_EXPERIENCE->value => 'Free Experience',
        ];
    }
}
