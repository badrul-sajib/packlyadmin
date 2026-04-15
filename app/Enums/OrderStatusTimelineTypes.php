<?php

namespace App\Enums;

enum OrderStatusTimelineTypes: int
{
    case ORDER   = 1;
    case RETURN  = 2;
    case PAYMENT = 3;
}
