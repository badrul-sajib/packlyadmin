<?php

namespace Modules\Api\V1\Ecommerce\Payment\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Services\Payments\PaymentFactory;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PaymentController extends Controller
{
    /**
     * @throws Throwable
     */
    public function payment(Order $order)
    {
        $paymentProcessor = PaymentFactory::getProcessor(PaymentMethod::SSLCOMMERZ->value);
        $paymentResult    = $paymentProcessor->process($order);

        return success('Order payment processed successfully', [
            'payment' => $paymentResult,
        ], Response::HTTP_ACCEPTED);
    }
}
