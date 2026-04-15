<?php

namespace App\Services\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order\Order;
use Exception;

class CodPayment implements PaymentProcessor
{
    /**
     * @throws Exception
     */
    public function process(Order $order, array $data = []): array
    {
        $merchantOrders = $order->merchantOrders()->get();

        foreach ($merchantOrders as $merchantOrder) {
            $tranId = getInvoiceNo('order_payments', 'tran_id', 'COD');
            $merchantOrder->payments()->create([
                'merchant_id'    => $merchantOrder->merchant_id,
                'tran_id'        => $tranId,
                'amount'         => $merchantOrder->total_amount,
                'payment_method' => PaymentMethod::COD,
                'payment_status' => PaymentStatus::PENDING,
                'payment_ref'    => $data['payment_ref'] ?? null,
            ]);
        }

        return ['is_redirect' => false, 'redirect_url' => null, 'message' => 'Payment successful.'];
    }
}
