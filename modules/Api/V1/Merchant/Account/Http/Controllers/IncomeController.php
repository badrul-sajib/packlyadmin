<?php

namespace Modules\Api\V1\Merchant\Account\Http\Controllers;

use App\Enums\AccountTypes;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Account\Http\Requests\IncomeRequest;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Merchant\Transaction;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class IncomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-income')->only('index');
        $this->middleware('shop.permission:create-income')->only('store');
    }

    public function index(Request $request): JsonResponse
    {
        $perPage   = $request->query('per_page', 10);
        $transfers = Transaction::where('merchant_id', auth()->user()->merchant->id)
            ->where('transaction_type', 'other-income')
            ->select('id', 'type', 'amount', 'note', 'ref_no')
            ->paginate($perPage);

        return ApiResponse::formatPagination('Owner Income List', $transfers, Response::HTTP_OK);
    }

    public function store(IncomeRequest $request): JsonResponse
    {
        try {
            $merchantId = $request->user()?->merchant->id;
            $request->validated();

            $account = Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::INCOME->value, 'uucode' => 'REVE'])->first();

            if (! $account) {
                $account = Account::create([
                    'merchant_id'  => $merchantId,
                    'account_type' => AccountTypes::INCOME->value,
                    'account_name' => 'Revenue',
                    'uucode'       => 'REVE',
                    'slug'         => 'revenue',
                    'balance'      => 0,
                    'code'         => 'RV',
                ]);
            }

            DB::beginTransaction();

            $account->increment('balance', $request->amount);

            Account::where('id', $request->from_account_id)->where('merchant_id', $merchantId)->increment('balance', $request->amount);

            $uuid = Str::uuid();

            $debit = MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $request->from_account_id,
                'amount'      => $request->amount,
                'date'        => $request->date ?? now(),
                'reason'      => 'Other Income from ' . $account->account_name . ': ' . $request->reason,
                'note'        => $request->description,
                'reference'   => $request->reference,
                'type'        => 'debit',
            ]);

            $credit = MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $account->id,
                'amount'      => $request->amount,
                'date'        => $request->date ?? now(),
                'reason'      => 'Other Income from ' . $account->account_name . ': ' . $request->reason,
                'note'        => $request->description,
                'reference'   => $request->reference,
                'type'        => 'credit',
            ]);

            $credit->attachment = $request->attachment;
            $credit->save();

            $debit->attachment = $request->attachment;
            $debit->save();

            DB::commit();

            return ApiResponse::successMessageForCreate('Income Added Successfully', [], Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            $errors = $v->validator->errors();

            return ApiResponse::validationError('There were validation errors.', $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
