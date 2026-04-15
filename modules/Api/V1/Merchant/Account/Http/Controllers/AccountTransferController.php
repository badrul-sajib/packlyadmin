<?php

namespace Modules\Api\V1\Merchant\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Account\Http\Requests\AccountTransferRequest;
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

class AccountTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-account-transfer')->only('index');
        $this->middleware('shop.permission:create-account-transfer')->only('store');
    }

    public function index(Request $request): JsonResponse
    {
        $perPage   = $request->query('per_page', 10);
        $transfers = Transaction::where('merchant_id', auth()->user()->merchant->id)
            ->where('transaction_type', 'transfer-to-other-account')
            ->select('id', 'type', 'amount', 'note', 'ref_no')
            ->paginate($perPage);

        return ApiResponse::formatPagination('Account Transfers List', $transfers, Response::HTTP_OK);
    }

    public function store(AccountTransferRequest $request): JsonResponse
    {
        try {
            $merchantId = $request->user()?->merchant->id;

            $data = $request->validated();

            if ($data['to_account_id'] == $data['from_account_id']) {
                return ApiResponse::failure('To account and from account should not be same.', Response::HTTP_CONFLICT);
            }

            $fromAccount = Account::where('id', $request->from_account_id)
                ->where('merchant_id', $request->user()->merchant->id)
                ->first();

            if ($fromAccount->balance < $data['amount']) {
                return ApiResponse::failure('Insufficient balance', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::beginTransaction();

            $fromAccount->decrement('balance', $request->amount);

            Account::where('id', $request->to_account_id)
                ->where('merchant_id', $request->user()->merchant->id)
                ->increment('balance', $request->amount);

            $uuid  = Str::uuid();
            $debit = MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $request->from_account_id,
                'amount'      => -$request->amount,
                'date'        => $request->date ?? now(),
                'reason'      => 'Account Transfer: ' . $request->reason,
                'type'        => 'debit',
                'note'        => $request->description,
                'reference'   => $request->reference,
            ]);

            $credit = MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $request->to_account_id,
                'amount'      => $request->amount,
                'date'        => $request->date ?? now(),
                'reason'      => 'Account Transfer: ' . $request->reason,
                'type'        => 'credit',
                'note'        => $request->description,
                'reference'   => $request->reference,
            ]);

            $debit->attachment = $request->attachment;
            $debit->save();

            $credit->attachment = $request->attachment;
            $credit->save();
            DB::commit();

            return ApiResponse::successMessageForCreate('transaction Created Successfully', [], Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            $errors = $v->validator->errors();

            return ApiResponse::validationError('There were validation errors.', $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('brand not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Account not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
