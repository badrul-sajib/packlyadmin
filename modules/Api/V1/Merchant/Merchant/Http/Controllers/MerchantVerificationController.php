<?php

namespace Modules\Api\V1\Merchant\Merchant\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Merchant\Http\Requests\MerchantVerificationRequest;
use App\Services\ApiResponse;
use App\Services\Merchant\MerchantVerificationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MerchantVerificationController extends Controller
{
    public function __construct(
        private MerchantVerificationService $merchantVerificationService
    ) {
        $this->middleware('shop.permission:submit-merchant-verification')->only('submit');
    }

    public function submit(MerchantVerificationRequest $request): JsonResponse
    {
        try {
            $this->merchantVerificationService->submit($request->validated());

            return ApiResponse::success('Merchant verification submitted successfully.');
        } catch (Exception $e) {
            Log::error($e);

            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
