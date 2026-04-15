<?php

namespace App\Enums;

enum CustomerTypes: int
{
    case B2B = 1;

    public function getValues(): string
    {
        return match ($this) {
            self::B2B => 'B2B'
        };
    }
}
