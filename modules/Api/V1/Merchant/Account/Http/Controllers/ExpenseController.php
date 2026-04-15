<?php

namespace Modules\Api\V1\Merchant\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Account\Http\Requests\ExpenseRequest;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantTransaction;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:create-expense')->only('store');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(ExpenseRequest $request): JsonResponse
    {
        try {
            $merchantId = $request->user()?->merchant->id;
            $request->validated();

            $fromAccount = Account::where('id', $request->from_account_id)->where('merchant_id', $merchantId)->first();

            if ($fromAccount->balance < $request->amount) {
                return ApiResponse::failure('Insufficient balance', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $toAccount = Account::where('id', $request->to_account_id)->where('merchant_id', $merchantId)->first();

            DB::beginTransaction();

            $fromAccount->decrement('balance', $request->amount);

            $toAccount->increment('balance', $request->amount);

            $uuid = Str::uuid();
            // credit operation while money goes out
            $credit = MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $request->from_account_id,
                'amount'      => -$request->amount,
                'date'        => $request->date ?? now(),
                'reason'      => 'Expense Payment from this account: ' . $request->reason,
                'type'        => 'credit',
                'note'        => $request->description,
                'reference'   => $request->reference,
            ]);

            // debit operation while money goes in
            $debit = MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $request->to_account_id,
                'amount'      => $request->amount,
                'date'        => $request->date ?? now(),
                'reason'      => 'Expense Payment to this account: ' . $request->reason,
                'type'        => 'debit',
                'note'        => $request->description,
                'reference'   => $request->reference,
            ]);

            if ($request->hasFile('attachment')) {
                $debit->attachment  = $request->attachment;
                $credit->attachment = $request->attachment;
                $debit->save();
                $credit->save();
            }

            DB::commit();

            return ApiResponse::successMessageForCreate('Expense Created Successfully', [], Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            $errors = $v->validator->errors();

            return ApiResponse::validationError('There were validation errors.', $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Expense not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
