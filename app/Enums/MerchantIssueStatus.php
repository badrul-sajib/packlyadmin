<?php

namespace App\Enums;

enum MerchantIssueStatus: int
{
    case Pending    = 0;
    case InProgress = 1;
    case Resolved   = 2;

    public static function labels(): array
    {
        return [
            self::Pending->value     => 'pending',
            self::InProgress->value  => 'in-progress',
            self::Resolved->value    => 'resolved',
        ];
    }
}
