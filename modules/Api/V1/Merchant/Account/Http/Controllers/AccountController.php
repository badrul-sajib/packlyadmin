<?php

namespace Modules\Api\V1\Merchant\Account\Http\Controllers;

use App\Enums\AccountTypes;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Account\Http\Requests\AccountRequest;
use App\Models\Account\Account;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-account')->only('index');
        $this->middleware('shop.permission:create-account')->only('store');
        $this->middleware('shop.permission:update-account')->only('update');
        $this->middleware('shop.permission:delete-account')->only('destroy');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $accountChartsQuery = Account::where('merchant_id', Auth::user()->merchant->id);

            if ($request->has('type_id') && $request->type_id !== null) {
                $accountChartsQuery->where(function ($query) use ($request) {
                    $query->where('account_type_id', $request->type_id)->orWhereNull('account_type_id');
                });
            }

            $perPage = $request->query('per_page', 10);

            $accountCharts = $accountChartsQuery->orderBy('id', 'desc')->paginate($perPage);

            return ApiResponse::formatPagination('All Accounts retrieved successfully', $accountCharts, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Account not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(AccountRequest $request, Account $account): JsonResponse
    {
        try {
            $request->validated();

            $request['slug'] = Str::slug($request->name);
            $account->update($request->all());

            return ApiResponse::success('Account updated successfully', $account, Response::HTTP_OK);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors.', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Account not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(AccountRequest $request): JsonResponse
    {
        try {
            $request->validated();

            $request['merchant_id'] = $request->user()->merchant->id;
            $request['status']      = 1;
            $request['balance']     = $request->balance ?? 0;
            $request['slug']        = Str::slug($request->name);
            $request['uucode']      = Str::random(6);
            $account                = Account::create($request->all());

            return ApiResponse::success('Account Added successfully', $account, Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Account not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function totalBalance(): JsonResponse
    {
        try {
            $totalCashBalance = Account::where(['merchant_id' => Auth::user()->merchant->id, 'account_type' => AccountTypes::CASH->value])->sum('balance');
            $totalBankBalance = Account::where(['merchant_id' => Auth::user()->merchant->id, 'account_type' => AccountTypes::BANK->value])->sum('balance');

            return ApiResponse::success('Total Balance', ['total_cash_balance' => (float) $totalCashBalance, 'total_bank_balance' => (float) $totalBankBalance]);
        } catch (Exception $e) {
            return ApiResponse::failure('Account not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
