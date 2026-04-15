<?php

namespace App\Enums;

enum PayoutRecurringTypes:int
{
    case DAILY = 1;
    case WEEKLY = 2;
    case MONTHLY = 3;

    public function label(): string
    {
        return match ($this) {
            PayoutRecurringTypes::DAILY => 'Daily',
            PayoutRecurringTypes::WEEKLY => 'Weekly',
            PayoutRecurringTypes::MONTHLY => 'Monthly',
        };
    }
}
