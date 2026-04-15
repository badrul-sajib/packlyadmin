<?php

namespace Modules\Api\V1\Merchant\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment\PayoutBeneficiary;
use App\Models\Payment\PayoutBeneficiaryMobileWallet;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PayoutBeneficiaryMobileWalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-payout-mobile-wallet-beneficiaries')->only('index');
    }

    public function index(): JsonResponse
    {
        try {
            $beneficiaries = PayoutBeneficiaryMobileWallet::where(['status' => 1])->select('id', 'name', 'image')->get();

            return ApiResponse::success(
                'Payout Beneficiary Mobile Wallets retrieved successfully',
                $beneficiaries,
                Response::HTTP_OK,
            );
        } catch (Exception $e) {
            return ApiResponse::error('Failed to fetch beneficiaries. Error ', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function MobileWalletWisePayoutBeneficiary(): JsonResponse
    {
        try {
            $beneficiaries = PayoutBeneficiaryMobileWallet::where('status', 1)
                ->with([
                    'payoutBeneficiaries' => function ($query) {
                        $query->select(
                            'id',
                            'payout_beneficiary_mobile_wallet_id',
                            'account_number'
                        );
                    },
                ])
                ->select('id', 'name', 'image')
                ->get()
                ->map(function ($wallet) {
                    // Single beneficiary expected → get first account number
                    $accountNumber = PayoutBeneficiary::where(['payout_beneficiary_mobile_wallet_id' => $wallet->id, 'merchant_id' => Auth::user()->merchant->id])->first()->account_number ?? null;

                    return [
                        'id' => $wallet->id,
                        'name' => $wallet->name,
                        'account_number' => $accountNumber,
                        'image' => $wallet->image,
                    ];
                });

            return ApiResponse::success(
                'Payout Beneficiary Mobile Wallets retrieved successfully',
                $beneficiaries,
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                'Failed to fetch beneficiaries. Error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
