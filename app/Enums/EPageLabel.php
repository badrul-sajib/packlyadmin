<?php

namespace App\Enums;

enum EPageLabel: int
{
    case ABOUT  = 1;
    case HELP   = 2;
    case NAVBAR = 3;
    case FIXED  = 4;

    public static function all(): array
    {
        return [
            self::ABOUT->value  => 'About',
            self::HELP->value   => 'Help',
            self::NAVBAR->value => 'Navbar',
            self::FIXED->value  => 'Fixed',
        ];
    }
}
