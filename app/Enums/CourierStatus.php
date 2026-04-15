<?php

namespace App\Enums;

enum CourierStatus: string
{
    case IN_REIVEW = 'in_review';
    case PENDING = 'pending';
    case DELIVERED = 'delivered';
    case PARTIAL_DELIVERED = 'partial_delivered';
    case CANCELLED = 'cancelled';
    case UNKNOWN = 'unknown';
}
