<?php

namespace App\Enums;

enum MerchantReportStatus: string
{
    case Pending    = 'pending';
    case InProgress = 'in-progress';
    case Resolved   = 'resolved';

    public static function getValues(): array
    {
        return [
            self::Pending,
            self::InProgress,
            self::Resolved,
        ];
    }
}
