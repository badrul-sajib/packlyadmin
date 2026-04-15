<?php

namespace Modules\Api\V1\Merchant\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment\PayoutBeneficiaryBank;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PayoutBeneficiaryBankController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-payout-bank-beneficiaries')->only('index');
    }

    public function index(): JsonResponse
    {
        try {
            $beneficiaries = PayoutBeneficiaryBank::where(['status' => 1])->select('id', 'name')->get();

            return ApiResponse::success(
                'Payout Beneficiary Banks retrieved successfully',
                $beneficiaries,
                Response::HTTP_OK,
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to fetch beneficiaries. Error ' , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
