<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case COD        = 'COD';
    case SSLCOMMERZ = 'SSLCommerz';

    public static function getLabels(): array
    {
        return [
            self::COD->value        => 'Cash on Delivery',
            self::SSLCOMMERZ->value => 'SSLCommerz',
        ];
    }
}
