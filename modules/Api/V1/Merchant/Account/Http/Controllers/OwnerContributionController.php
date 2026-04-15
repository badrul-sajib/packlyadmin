<?php

namespace Modules\Api\V1\Merchant\Account\Http\Controllers;

use App\Enums\AccountTypes;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Account\Http\Requests\OwnerContributionRequest;
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

class OwnerContributionController extends Controller
{

    public function __construct()
    {
        $this->middleware('shop.permission:show-owner-contribution')->only('index');
        $this->middleware('shop.permission:create-owner-contribution')->only('store');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage   = $request->query('per_page', 10);
        $transfers = Transaction::where('merchant_id', auth()->user()->merchant->id)
            ->where('transaction_type', 'owner-contribution')
            ->select('id', 'type', 'amount', 'note', 'ref_no')
            ->paginate($perPage);

        return ApiResponse::formatPagination('Owner Contribution List', $transfers, Response::HTTP_OK);
    }

    public function store(OwnerContributionRequest $request): JsonResponse
    {
        try {
            $merchantId = $request->user()?->merchant->id;
            $request->validated();

            DB::beginTransaction();

            Account::where('id', $request->from_account_id)->where('merchant_id', $merchantId)->increment('balance', $request->amount);

            $ownerCapital = Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::EQUITY->value, 'uucode' => 'OWCA'])->first();

            if (! $ownerCapital) {
                $ownerCapital = Account::create([
                    'merchant_id'  => $merchantId,
                    'account_type' => AccountTypes::EQUITY->value,
                    'account_name' => 'Owner Capital',
                    'uucode'       => 'OWCA',
                    'balance'      => 0,
                    'code'         => 'OC',
                ]);
            }

            $ownerCapital->balance += $request->amount;
            $ownerCapital->save();

            $uuid = Str::uuid();

            $debit = MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $request->from_account_id,
                'amount'      => $request->amount,
                'date'        => $request->date ?? now(),
                'reason'      => 'Owner Contribution from ' . $request->from_account_id . ': ' . $request->reason,
                'reference'   => $request->reference,
                'note'        => $request->description,
                'type'        => 'debit',
            ]);

            $debit->attachment = $request->attachment;
            $debit->save();

            $credit = MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $ownerCapital->id,
                'amount'      => $request->amount,
                'date'        => $request->date ?? now(),
                'reason'      => 'Owner Contribution from ' . $request->from_account_id . ': ' . $request->reason,
                'reference'   => $request->reference,
                'note'        => $request->description,
                'type'        => 'credit',
            ]);

            $credit->attachment = $request->attachment;
            $credit->save();

            DB::commit();

            return ApiResponse::successMessageForCreate('Owner Contribution Added Successfully', [], Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            $errors = $v->validator->errors();

            return ApiResponse::validationError('There were validation errors.', $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Owner Contribution not added.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
