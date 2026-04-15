<?php

namespace Modules\Api\V1\Merchant\Account\Http\Controllers;

use App\Enums\AccountTypes;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Account\Http\Requests\WithdrawRequest;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Merchant\Transaction;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class WithdrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-withdraw')->only('index');
        $this->middleware('shop.permission:create-withdraw')->only('store');
    }

    public function index(Request $request): JsonResponse
    {
        $perPage   = $request->query('per_page', 10);
        $transfers = Transaction::where('merchant_id', auth()->user()->merchant->id)
            ->where('transaction_type', 'owner-withdraw')
            ->select('id', 'type', 'amount', 'note', 'ref_no')
            ->paginate($perPage);

        return ApiResponse::formatPagination('Owner Withdraw List', $transfers, Response::HTTP_OK);
    }

    public function store(WithdrawRequest $request): JsonResponse
    {
        try {
            $merchantId = $request->user()?->merchant->id;
            $data       = $request->validated();

            $account = Account::where('id', $request->from_account_id)
                ->where('merchant_id', $merchantId)
                ->first();

            $ownerDraw = Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::EQUITY->value, 'uucode' => 'DRAW'])->first();

            if ($account->balance < $data['amount']) {
                return ApiResponse::failure('Insufficient balance', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (! $ownerDraw) {
                $ownerDraw = Account::create([
                    'merchant_id'  => $merchantId,
                    'account_type' => AccountTypes::EQUITY->value,
                    'account_name' => 'Drawings',
                    'uucode'       => 'DRAW',
                    'slug'         => 'drawings',
                    'balance'      => 0,
                    'code'         => 'D',
                ]);
            }

            $ownerDraw->increment('balance', $request->amount);

            DB::beginTransaction();

            $account->decrement('balance', $request->amount);
            $uuid = Str::uuid();

            $credit = MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $request->from_account_id,
                'amount'      => -$request->amount,
                'date'        => $request->date ?? now(),
                'reason'      => 'Owner Withdraw from ' . $account->account_name . ': ' . $request->reason,
                'type'        => 'credit',
                'note'        => $request->description,
                'reference'   => $request->reference,
            ]);

            $credit->attachment = $request->attachment;
            $credit->save();

            $debit = MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $ownerDraw->id,
                'amount'      => $request->amount,
                'date'        => $request->date ?? now(),
                'reason'      => 'Owner Withdraw to ' . $ownerDraw->account_name . ': ' . $request->reason,
                'type'        => 'debit',
                'note'        => $request->description,
                'reference'   => $request->reference,
            ]);

            $debit->attachment = $request->attachment;
            $debit->save();

            DB::commit();

            return ApiResponse::successMessageForCreate('transaction Created Successfully', [], Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            $errors = $v->validator->errors();

            return ApiResponse::validationError('There were validation errors.', $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('brand not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
