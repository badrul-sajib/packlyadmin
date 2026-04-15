<?php

namespace App\Enums;

enum SliderType: int
{
    case HOME_HERO    = 1;
    case SHOP_DETAILS = 2;
    case HOME_BANNER  = 3;
    case CTA_1        = 4;
    case CTA_2        = 5;
    case CTA_3        = 6;
    case CTA_4        = 7;
    case CTA_5        = 8;

    public static function getLabel($getLabel): string
    {
        return match ($getLabel) {
            self::HOME_HERO    => 'Hero Section',
            self::SHOP_DETAILS => 'Shop Details',
            self::HOME_BANNER  => 'Home Banner 1',
            self::CTA_1        => 'Home Banner 2',
            self::CTA_2        => 'Home Banner 3',
            self::CTA_3        => 'Home Banner 4',
            self::CTA_4        => 'Home Banner 5',
            self::CTA_5        => 'Home Banner 6',
            default            => 'Select type',
        };
    }
}
