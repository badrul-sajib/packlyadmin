<?php

namespace App\Enums;

enum CommonType: int
{
    case EXCLUDE = 1;
    case INCLUDE = 2;

    public static function label(): array
    {
        return [
            self::EXCLUDE->value => 'Exclude',
            self::INCLUDE->value => 'Include',
        ];
    }

    public function color(): string
    {
        return match ($this) {
            self::EXCLUDE => 'red',
            self::INCLUDE => 'green',
        };
    }
}
