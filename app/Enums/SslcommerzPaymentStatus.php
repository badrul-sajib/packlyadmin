<?php

namespace App\Enums;

enum SslcommerzPaymentStatus: string
{
    case VALID       = 'VALID';
    case FAILED      = 'FAILED';
    case CANCELLED   = 'CANCELLED';
    case UNATTEMPTED = 'UNATTEMPTED';
    case EXPIRED     = 'EXPIRED';
}
