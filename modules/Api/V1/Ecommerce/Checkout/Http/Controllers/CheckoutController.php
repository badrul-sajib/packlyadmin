<?php

namespace Modules\Api\V1\Ecommerce\Checkout\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Checkout\Http\Requests\CheckoutRequest;
use App\Services\Checkout\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __construct(private readonly CheckoutService $checkoutService) {}

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->checkoutService->checkout($data);
    }
}
