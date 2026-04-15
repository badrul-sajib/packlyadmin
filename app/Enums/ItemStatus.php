<?php

namespace App\Enums;

enum ItemStatus: int
{
    case PENDING    = 0;
    case ACCEPTED   = 7;
    case APPROVED   = 1;
    case REJECTED   = 2;
    case COMPLETED  = 3;
    case PROCESSING = 4;
    case DECLINED   = 5;
    case REFUNDED   = 6;
    case CANCELLED  = 8;

    public static function getLabel(): array
    {
        return [
            self::PENDING->value    => 'Pending',
            self::ACCEPTED->value   => 'Accepted',
            self::APPROVED->value   => 'Approved',
            self::REJECTED->value   => 'Rejected',
            self::COMPLETED->value  => 'Completed',
            self::PROCESSING->value => 'Processing',
            self::DECLINED->value   => 'Declined',
            self::REFUNDED->value   => 'Refunded',
            self::CANCELLED->value  => 'Cancelled',
        ];
    }
}
