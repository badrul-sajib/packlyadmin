<?php

namespace App\Enums;

enum OrderStatus: int
{
    case PENDING           = 1;
    case APPROVED          = 2;
    case PROCESSING        = 3;
    case DELIVERED         = 4;
    case CANCELLED         = 5;
    case RETURN_REQUEST    = 6;
    case RETURNED          = 7;
    case REFUNDED          = 8;
    case READY_TO_SHIP     = 9;
    case PARTIAL_DELIVERED = 10;
    case UNKNOWN           = 11;

    public static function getStatusLabels(): array
    {
        return [
            self::PENDING->value           => 'Pending',
            self::APPROVED->value          => 'Approved',
            self::PROCESSING->value        => 'Processing',
            self::DELIVERED->value         => 'Delivered',
            self::CANCELLED->value         => 'Cancelled',
            self::RETURN_REQUEST->value    => 'Return Request',
            self::RETURNED->value          => 'Returned',
            self::REFUNDED->value          => 'Refunded',
            self::READY_TO_SHIP->value     => 'Ready to Ship',
            self::PARTIAL_DELIVERED->value => 'Partial Delivered',
            self::UNKNOWN->value           => 'Unknown',
        ];
    }

    public static function status_by_color(): array
    {
        return [
            self::PENDING->value           => 'alert-warning',
            self::PROCESSING->value        => 'alert-info',
            self::APPROVED->value          => 'alert-success',
            self::DELIVERED->value         => 'alert-success',
            self::CANCELLED->value         => 'alert-danger',
            self::RETURN_REQUEST->value    => 'alert-warning',
            self::RETURNED->value          => 'alert-danger',
            self::REFUNDED->value          => 'alert-success',
            self::READY_TO_SHIP->value     => 'alert-info',
            self::PARTIAL_DELIVERED->value => 'alert-info',
            self::UNKNOWN->value           => 'alert-danger',
        ];
    }

    public static function getStatusLabel($value): string
    {
        return self::getStatusLabels()[$value] ?? 'Unknown';
    }

    public static function getProductStatusLabels(): array
    {
        return [
            self::PENDING->value           => 'Pending', // 1
            self::APPROVED->value          => 'Approved',
            self::PROCESSING->value        => 'Processing', // 3
            self::CANCELLED->value         => 'Cancelled', // 5
            self::DELIVERED->value         => 'Delivered', // 4
            self::RETURN_REQUEST->value    => 'Return Requested', // 6
            self::RETURNED->value          => 'Returned', // 7
            self::REFUNDED->value          => 'Refunded', // 8
            self::READY_TO_SHIP->value     => 'Ready to Ship', // 9
            self::PARTIAL_DELIVERED->value => 'Partial Delivered', // 10
            self::UNKNOWN->value           => 'Unknown',
        ];
    }

    public static function getStatusMessage(): array
    {
        return [
            self::PENDING->value           => 'Order is pending for approval',
            self::APPROVED->value          => 'Order is approved and ready for processing',
            self::PROCESSING->value        => 'Order is processing and ready for shipping',
            self::DELIVERED->value         => 'Order is delivered successfully',
            self::CANCELLED->value         => 'Order is cancelled',
            self::RETURN_REQUEST->value    => 'Order is returned by the user',
            self::RETURNED->value          => 'Order is returned successfully',
            self::REFUNDED->value          => 'Order is refunded successfully',
            self::READY_TO_SHIP->value     => 'Order is ready to ship',
            self::PARTIAL_DELIVERED->value => 'Order is partially delivered',
            self::UNKNOWN->value           => 'Order is unknown',
        ];
    }
}
