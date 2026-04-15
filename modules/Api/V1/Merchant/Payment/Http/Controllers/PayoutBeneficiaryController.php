<?php

namespace Modules\Api\V1\Merchant\Payment\Http\Controllers;

use App\Enums\PayoutRecurringTypes;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Payment\Http\Requests\PayoutBeneficiaryRequest;
use Modules\Api\V1\Merchant\Payment\Http\Requests\SetDefaultPayoutBeneficiaryRequest;
use App\Models\Merchant\MerchantSetting;
use App\Models\Payment\PayoutBeneficiary;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PayoutBeneficiaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-payout-beneficiaries')->only('index', 'show');
        $this->middleware('shop.permission:create-payout-beneficiary')->only('store');
        $this->middleware('shop.permission:update-payout-beneficiary')->only('update');
        $this->middleware('shop.permission:delete-payout-beneficiary')->only('destroy');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $beneficiaries = PayoutBeneficiary::where('merchant_id', $request->user()->merchant->id);

            if ($request->has('type')) {
                $beneficiaries = $beneficiaries->where(
                    'payout_beneficiary_type_id',
                    $request->get('type')
                );
            }

            $beneficiaries = $beneficiaries
                ->with(['beneficiaryTypes', 'mobileWallet', 'bank'])
                ->get();

            $formattedBeneficiaries = $beneficiaries->map(function ($beneficiary) {
                return [
                    'id' => $beneficiary->id,
                    'beneficiary_type' => $beneficiary->beneficiaryTypes->name,
                    'beneficiary_name' => $beneficiary->account_holder_name,
                    'beneficiary' => $beneficiary->getMobileWalletOrBankAttribute() ? [
                        'id' => $beneficiary->getMobileWalletOrBankAttribute()->id,
                        'name' => $beneficiary->getMobileWalletOrBankAttribute()->name,
                    ] : null,
                    'beneficiary_account' => $beneficiary->account_number,
                    'beneficiary_branch' => $beneficiary->branch_name,
                    'routing_number' => $beneficiary->routing_number,
                    'is_default' => (bool) $beneficiary->is_default,
                ];
            });

            return ApiResponse::success(
                'Beneficiaries retrieved successfully',
                $formattedBeneficiaries,
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to fetch beneficiaries. Error' ,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function store(PayoutBeneficiaryRequest $request, $payoutBeneficiary = null): JsonResponse
    {
        try {
            $validated = $request->validated();

            $data = array_merge([
                'payout_beneficiary_mobile_wallet_id' => null,
                'payout_beneficiary_bank_id' => null,
                'branch_name' => null,
                'routing_number' => null,
            ], $validated);

            $merchantId = $request->user()->merchant->id;
            $typeId = (int) $data['payout_beneficiary_type_id'];

            $isMobile = $typeId === 1;
            $isBank = $typeId === 2;

            if (!$isMobile && !$isBank) {
                return ApiResponse::validationError(
                    'Validation Failed',
                    ['payout_beneficiary_type_id' => ['Invalid payout beneficiary type.']],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $values = [
                'merchant_id' => $merchantId,
                'payout_beneficiary_type_id' => $typeId,
                'account_holder_name' => $data['account_holder_name'] ?? null,
                'account_number' => $data['account_number'],
                'branch_name' => $data['branch_name'],
                'routing_number' => $data['routing_number'],
                'payout_beneficiary_mobile_wallet_id' => $data['payout_beneficiary_mobile_wallet_id'],
                'payout_beneficiary_bank_id' => $data['payout_beneficiary_bank_id'],
            ];

            $beneficiary = $payoutBeneficiary ? PayoutBeneficiary::find($payoutBeneficiary) : null;

            if ($isMobile && !$beneficiary && $data['payout_beneficiary_mobile_wallet_id']) {

                $existing = PayoutBeneficiary::where('merchant_id', $merchantId)
                    ->where('payout_beneficiary_mobile_wallet_id', $data['payout_beneficiary_mobile_wallet_id'])
                    ->first();

                if ($existing) {
                    $beneficiary = $existing;
                }
            }

            if ($beneficiary) {
                $beneficiary->update($values);
                $message = 'Beneficiary updated successfully';
                $status = Response::HTTP_OK;
            } else {
                $beneficiary = PayoutBeneficiary::create($values);
                $message = 'Beneficiary created successfully';
                $status = Response::HTTP_CREATED;
            }

            return ApiResponse::successMessageForCreate(
                $message,
                $beneficiary->load(['mobileWallet', 'bank']),
                $status
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to create or update beneficiary. Error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    public function show(int $id): JsonResponse
    {
        try {
            $beneficiary = PayoutBeneficiary::where('merchant_id', request()->user()->merchant->id)
                ->with(['beneficiaryType', 'mobileWallet', 'bank'])
                ->findOrFail($id);

            return ApiResponse::success(
                'Beneficiary retrieved successfully',
                $beneficiary,
                Response::HTTP_OK
            );
        } catch (\Exception) {
            return ApiResponse::failure('Beneficiary not found', Response::HTTP_NOT_FOUND);
        }
    }

    public function setDefault(SetDefaultPayoutBeneficiaryRequest $request, $payoutBeneficiary): JsonResponse
    {
        $merchant = $request->user()->merchant;
        $isDefault = $request->boolean('is_default');

        if ($payoutBeneficiary && (int) $payoutBeneficiary !== 0) {
            // Load the beneficiary from the DB
            $beneficiary = PayoutBeneficiary::find($payoutBeneficiary);

            if (!$beneficiary || (int) $beneficiary->merchant_id !== (int) $merchant->id) {
                return ApiResponse::error(
                    'You are not allowed to modify this beneficiary.',
                    Response::HTTP_FORBIDDEN
                );
            }

            if ($isDefault) {
                // This one becomes default, others false
                $this->enforceDefaultBeneficiary($merchant->id, $beneficiary->id);
                $beneficiary->refresh();
            } else {
                // Explicitly false → just turn this one off
                $beneficiary->update(['is_default' => false]);
            }
        } else {
            // If $payoutBeneficiary is 0 or null → turn all off
            PayoutBeneficiary::where('merchant_id', $merchant->id)
                ->update(['is_default' => false]);

            $beneficiary = null;
        }

        // Update merchant setting
        MerchantSetting::where('merchant_id', $merchant->id)
            ->update(['payout_recurring_type' => $request->payout_recurring_type]);

        return ApiResponse::successMessageForCreate(
            'Default payout beneficiary updated successfully',
            $beneficiary?->load(['mobileWallet', 'bank']),
            Response::HTTP_OK
        );
    }



    protected function enforceDefaultBeneficiary(int $merchantId, int $beneficiaryId): void
    {
        DB::transaction(function () use ($merchantId, $beneficiaryId) {
            // Turn off default for all other beneficiaries of this merchant
            PayoutBeneficiary::where('merchant_id', $merchantId)
                ->where('id', '!=', $beneficiaryId)
                ->update(['is_default' => false]);

            // Ensure THIS one is default
            PayoutBeneficiary::where('id', $beneficiaryId)
                ->update(['is_default' => true]);
        });
    }

    public function payoutRecurringTypes(): JsonResponse
    {
        $merchant = request()->user()->merchant;

        $payoutRecurringType = MerchantSetting::where('merchant_id', $merchant->id)->first()->payout_recurring_type->value ?? null;

        $data = array_map(function ($case) use ($payoutRecurringType) {
            return [
                'value' => $case->value,
                'label' => $case->label(),
                'default' => $case->value === $payoutRecurringType,
            ];
        }, PayoutRecurringTypes::cases());

        return ApiResponse::success(
            'Payout recurring types retrieved successfully',
            $data,
            Response::HTTP_OK
        );
    }
}
