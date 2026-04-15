<?php

namespace Modules\Api\V1\Ecommerce\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Traits\SslcommerzPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SslcommerzPaymentController extends Controller
{
    use SslcommerzPayment;

    public function create(Order $order): JsonResponse
    {
        try {
            if ($order->user_id != auth()->id()) {
                return failure('Invalid order', Response::HTTP_NOT_FOUND);
            }

            return success('Sslcommerz payment created successfully', $this->createSslcommerzPayment($order));
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
