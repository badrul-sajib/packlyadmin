<?php

namespace App\Enums;

enum RecurringTypes: int
{
    case WEEKLY       = 1;
    case MONTHLY      = 2;
    case HALF_MONTHLY = 3;
    case QUARTERLY    = 4;
    case HALF_YEARLY  = 5;
    case YEARLY       = 6;
    case FOREVER      = 7;

    public function label(): string
    {
        return match ($this) {
            self::WEEKLY       => 'Weekly',
            self::MONTHLY      => 'Monthly',
            self::HALF_MONTHLY => 'Half-Monthly',
            self::QUARTERLY    => 'Quarterly',
            self::HALF_YEARLY  => 'Half-Yearly',
            self::YEARLY       => 'Yearly',
            self::FOREVER      => 'Forever',
        };
    }
}
