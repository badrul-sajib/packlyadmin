<?php

namespace App\Services\Payments;

use App\Enums\PaymentMethod;
use Exception;

class PaymentFactory
{
    /**
     * @throws Exception
     */
    public static function getProcessor(string $method)
    {
        return match ($method) {
            PaymentMethod::COD->value        => new CodPayment,
            PaymentMethod::SSLCOMMERZ->value => new SslCommerzPayment,
            default                          => throw new Exception('Invalid payment method'),
        };
    }
}
