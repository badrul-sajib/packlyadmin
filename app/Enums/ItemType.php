<?php

namespace App\Enums;

enum ItemType: string
{
    case CANCELLED = 'cancelled';
    case RETURNED  = 'return';
    case REFUNDED  = 'refund';
}
