<?php

namespace App\Traits;

use App\Enums\SslcommerzPaymentStatus;
use App\Models\Order\Order;
use App\Models\Payment\SslcommerzPayment as SslcommerzPaymentModel;

trait SslcommerzPayment
{
    public function createSslcommerzPayment(Order $order): SslcommerzPaymentModel
    {
        return SslcommerzPaymentModel::create([
            'order_id'       => $order->id,
            'transaction_id' => getInvoiceNo('sslcommerz_payments', 'transaction_id', 'SSL'),
            'payment_status' => SslcommerzPaymentStatus::UNATTEMPTED->value,
        ]);
    }

    public function findSslcommerzPayment(string $transaction_id): ?SslcommerzPaymentModel
    {
        return SslcommerzPaymentModel::where('transaction_id', $transaction_id)->first();
    }

    public function findSslcommerzPaymentForUpdate(string $transaction_id): ?SslcommerzPaymentModel
    {
        return SslcommerzPaymentModel::where('transaction_id', $transaction_id)->lockForUpdate()->first();
    }

    public function updateSslcommerzPaymentStatus(string $transaction_id, string $status): ?SslcommerzPaymentModel
    {
        $sslcommerzPayment = SslcommerzPaymentModel::where('transaction_id', $transaction_id)->first();

        if ($sslcommerzPayment) {
            if ($sslcommerzPayment->payment_status === SslcommerzPaymentStatus::VALID->value) {
                return $sslcommerzPayment;
            }
            $sslcommerzPayment->payment_status = $status;
            $sslcommerzPayment->save();
        }

        return $sslcommerzPayment;
    }
}
