<?php

namespace App\Enums;

enum PayoutBeneficiaryTypes: int
{
    case BANK          = 1;
    case MOBILE_WALLET = 2;

    public function label(): string
    {
        return match ($this) {
            self::BANK          => 'Bank',
            self::MOBILE_WALLET => 'Mobile Wallet',
        };
    }
}
