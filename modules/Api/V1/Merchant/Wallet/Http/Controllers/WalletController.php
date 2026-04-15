<?php

namespace Modules\Api\V1\Merchant\Wallet\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Wallet\Http\Requests\WalletRequest;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Wallet\Wallet;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('shop.permission:show-wallets')->only('index', 'show', 'getTransactions');
        $this->middleware('shop.permission:create-wallet')->only('store');
        $this->middleware('shop.permission:update-wallet')->only('update', 'status');
        $this->middleware('shop.permission:delete-wallet')->only('destroy');
    }

    public function index(Request $request): JsonResponse
    {
        $wallets = Account::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('account_type', [2, 10])
            ->when($request->bank_id, function ($query) use ($request) {
                $query->where('id', $request->bank_id);
            })
            ->orderBy('id', 'asc')
            ->get();

        return ApiResponse::success('All wallets retrieved successfully', $wallets, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WalletRequest $request): JsonResponse
    {
        try {
            $data                                                    = $request->validated();
            $request->available_balance ? $data['available_balance'] = $request->available_balance : $data['available_balance'] = 0;

            $wallet = Auth::user()->merchant->wallets()->create($data);

            return ApiResponse::successMessageForCreate('Wallet created successfully.', $wallet, Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $wallet = Wallet::where('merchant_id', Auth::user()->merchant->id)->where('id', $id)->firstOrFail();

            return ApiResponse::success('Wallet retrieved successfully.', $wallet, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::failure('wallet not found.', Response::HTTP_NOT_FOUND);
        }
    }

    public function status(int $id): JsonResponse
    {
        try {
            $wallet = Wallet::where('merchant_id', Auth::user()->merchant->id)->findOrFail($id);

            $wallet->update(['status' => $wallet->status == '1' ? '0' : '1']);

            return ApiResponse::success('Wallet status updated successfully.', $wallet, Response::HTTP_OK);
        } catch (ModelNotFoundException $m) {
            return ApiResponse::failure('wallet not found.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WalletRequest $request, int $id): JsonResponse
    {
        try {
            $wallet = Wallet::where('merchant_id', Auth::user()->merchant->id)->findOrFail($id);

            $request->validated();

            $wallet->update([
                'merchant_id'       => Auth::user()->merchant->id,
                'wallet_type'       => $request->wallet_type,
                'name'              => $request->name,
                'account_number'    => $request->account_number,
                'bank_name'         => $request->bank_name,
                'branch_name'       => $request->branch_name,
                'route_no'          => $request->route_no,
                'available_balance' => $request->available_balance ?? 0.00,
            ]);

            return ApiResponse::success('Wallet updated successfully.', $wallet, Response::HTTP_OK);
        } catch (ModelNotFoundException $m) {
            return ApiResponse::failure('wallet not found.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $wallet = Wallet::where('merchant_id', Auth::user()->merchant->id)->findOrFail($id);

            $wallet->delete();

            return ApiResponse::success('Wallet deleted successfully.', [], Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('wallet not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTransactions(int $id, Request $request): JsonResponse
    {
        try {
            $transactionQuery = MerchantTransaction::where('merchant_id', Auth::user()->merchant->id)
                ->where('account_id', $id)
                ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                })
                ->orderBy('id', 'desc');

            $perPage      = $request->query('per_page', 10);
            $transactions = $transactionQuery->orderBy('id', 'desc')->paginate($perPage);

            return ApiResponse::formatPagination('Transactions retrieved successfully.', $transactions, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('wallet not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
