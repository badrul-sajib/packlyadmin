<?php

namespace Modules\Api\V1\Merchant\Account\Http\Controllers;

use App\Enums\AccountTypes;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Account\Http\Requests\BeneficiaryAccountRequest;
use App\Models\Account\Account;
use App\Models\Account\BeneficiaryAccount;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class BeneficiaryAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-beneficiary-account')->only('index');
        $this->middleware('shop.permission:create-beneficiary-account')->only('store');
        $this->middleware('shop.permission:update-beneficiary-account')->only('update');
        $this->middleware('shop.permission:delete-beneficiary-account')->only('destroy');
    }
    public function index(): JsonResponse
    {
        try {
            $accounts = Account::where('merchant_id', Auth::user()->merchant->id)->where('account_type', AccountTypes::BANK->value)
                ->orderBy('id', 'desc')
                ->get();

            return ApiResponse::success('All Beneficiary Accounts retrieved successfully', ['accounts' => $accounts], Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Account not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(BeneficiaryAccountRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $data['merchant_id'] = Auth::user()->merchant->id;
            $account             = BeneficiaryAccount::create($data);

            return ApiResponse::success('Beneficiary account created successfully.', $account, Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors.', $v->validator->errors(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            return ApiResponse::failure('Beneficiary account not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $account = BeneficiaryAccount::where('merchant_id', Auth::user()->merchant->id)->findOrFail($id);

            return ApiResponse::success('Beneficiary account retrieved successfully.', $account, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::failure('Beneficiary account not found.', Response::HTTP_NOT_FOUND);
        }
    }

    public function update(BeneficiaryAccountRequest $request, int $id): JsonResponse
    {
        try {
            $account = BeneficiaryAccount::where('merchant_id', Auth::user()->merchant->id)->findOrFail($id);

            $data = $request->validated();

            $account->update($data);

            return ApiResponse::success('Beneficiary account updated successfully.', $account, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Beneficiary account not found.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors.', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Beneficiary account not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function status(int $id): JsonResponse
    {
        try {
            $account = BeneficiaryAccount::where('merchant_id', Auth::user()->merchant->id)->findOrFail($id);

            $account->update(['status' => $account->status == '1' ? '0' : '1']);

            return ApiResponse::success('account status updated successfully.', $account, Response::HTTP_OK);
        } catch (ModelNotFoundException $m) {
            return ApiResponse::failure('beneficiary account not found.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Beneficiary account not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $account = BeneficiaryAccount::where('merchant_id', Auth::user()->merchant->id)->findOrFail($id);

            if ($account->transfersTo()->exists() || $account->transfersFrom()->exists()) {
                return ApiResponse::failure('Cannot delete this beneficiary account as it is associated with other records.', Response::HTTP_CONFLICT);
            }
            $account->delete();

            return ApiResponse::success('Beneficiary account deleted successfully.', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Beneficiary account not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Beneficiary account not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
