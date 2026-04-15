<?php

namespace App\Enums;

enum ReviewStatus: int
{
    case IS_PUBLIC       = 1;
    case IS_NOT_PUBLIC   = 2;
    case IS_APPROVED     = 3;
    case IS_NOT_APPROVED = 4;

    public static function getReviewStatuses(): array
    {
        return [
            self::IS_PUBLIC->value       => 'Public',
            self::IS_NOT_PUBLIC->value   => 'Not Public',
            self::IS_APPROVED->value     => 'Approved',
            self::IS_NOT_APPROVED->value => 'Not Approved',
        ];
    }
}
