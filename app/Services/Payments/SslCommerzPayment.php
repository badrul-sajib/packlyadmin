<?php

namespace App\Services\Payments;

use App\Models\Order\Order;
use Illuminate\Http\Request;

class SslCommerzPayment implements PaymentProcessor
{
    public function process(Order $order, array $data = []): array
    {
        // Static mode: SSLCommerz payment gateway is disabled
        return [
            'is_redirect'  => false,
            'message'      => 'Payment gateway is disabled in static mode.',
        ];
    }

    public function success(Request $request)
    {
        return redirect('/');
    }

    public function fail(Request $request)
    {
        return redirect('/');
    }

    public function cancel(Request $request)
    {
        return redirect('/');
    }

    public function ipn(Request $request)
    {
        return response()->json(['status' => 'ok']);
    }
}
