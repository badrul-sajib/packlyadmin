<?php

namespace Modules\Api\V1\Merchant\Supplier\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Supplier\Http\Requests\DuePaymentRequest;
use Modules\Api\V1\Merchant\Supplier\Http\Requests\SupplierRequest;
use App\Models\Merchant\Transaction;
use App\Models\Purchase\Purchase;
use App\Models\Supplier\Supplier;
use App\Models\Supplier\SupplierPayment;
use App\Models\Supplier\SupplierPurchasePaymentDetail;
use App\Models\Wallet\Wallet;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-suppliers')->only('index', 'show', 'supplierDueReport');
        $this->middleware('shop.permission:create-supplier')->only('store');
        $this->middleware('shop.permission:update-supplier')->only('update');
        $this->middleware('shop.permission:delete-supplier')->only('destroy');
        $this->middleware('shop.permission:make-supplier-due-payment')->only('duePayment');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $suppliersQuery = Supplier::with('purchases')
                ->where('merchant_id', auth()->user()->merchant->id);

            if ($request->has(key: 'search')) {
                $suppliersQuery->where(function ($query) use ($request) {
                    $query->whereAny(['name', 'phone'], 'LIKE', "%{$request->search}%");
                });
            }
            $perPage   = $request->query('per_page', 10);
            $suppliers = $suppliersQuery->orderBy('id', 'desc')->paginate($perPage);

            $suppliers->getCollection()->transform(function ($supplier) {
                $supplier->due_amount = $supplier->purchases()->sum('due_amount');

                return $supplier;
            });

            return ApiResponse::formatPagination('suppliers retrieved successfully', $suppliers, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function store(SupplierRequest $request): JsonResponse
    {
        try {
            $request->validated();

            if (! auth()->user()->merchant) {
                return response()->json(['message' => 'You are not authorized to access this resource'], Response::HTTP_UNAUTHORIZED);
            }

            $supplier = Supplier::create([
                'merchant_id'    => auth()->user()->merchant?->id,
                'name'           => $request->name,
                'contact_person' => $request->contact_person,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'address'        => $request->address,
                'balance'        => 0,
            ]);
            $supplier->image = $request->image;
            $supplier->save();

            return ApiResponse::successMessageForCreate('Supplier Created Successfully', $supplier, Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            $errors = $v->validator->errors();

            return ApiResponse::validationError('There were validation errors. ', $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
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
            $supplier = Supplier::where('merchant_id', Auth::user()->merchant->id)
                ->where('id', $id)
                ->firstOrFail();

            $total_purchase = $supplier->purchases()->count();

            $total_amount = $supplier->purchases()->sum('grand_total');

            $total_paid = $supplier->purchases()->sum('paid_amount');

            $total_due = $supplier->purchases()->sum('due_amount');

            return ApiResponse::success('supplier retrieved successfully.', [
                'supplier'       => $supplier,
                'total_purchase' => $total_purchase,
                'total_amount'   => $total_amount,
                'total_paid'     => $total_paid,
                'total_due'      => $total_due,
            ]);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('supplier not found.', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Display Supplier Purchase Due Report
     */
    public function supplierDueReport(Request $request, int $id): JsonResponse
    {
        try {
            $supplier = Supplier::where('merchant_id', Auth::user()->merchant->id)->findOrFail($id);

            $perPage = (int) $request->query('per_page', 10);

            $purchases = $supplier->purchases()
                ->select('id', 'purchase_invoice_no', 'note', 'ref_no', 'total_item', 'grand_total', 'return_amount', 'paid_amount', 'due_amount', 'status', 'purchase_date')
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $purchases->getCollection()->transform(function ($purchase) {
                $purchase->due_amount = ($purchase->grand_total ?? 0) - ($purchase->paid_amount ?? 0);
                return $purchase;
            });

            return ApiResponse::formatPagination('supplier due report retrieved successfully', $purchases, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('supplier not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(SupplierRequest $request, int $id): JsonResponse
    {
        try {
            $supplier = Supplier::where('merchant_id', auth()->user()->merchant->id)
                ->where('id', $id)
                ->firstOrFail();

            $request->validated();

            $supplier->update([
                'name'           => $request->name,
                'contact_person' => $request->contact_person,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'address'        => $request->address,
                'balance'        => $request->balance,
            ]);

            $supplier->image = $request->image;
            $supplier->save();

            return ApiResponse::success('Supplier updated successfully.', $supplier, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Supplier not found', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $supplier = Supplier::where('merchant_id', Auth::user()->merchant->id)->where('id', $id)->firstOrFail();

            if ($supplier) {
                $purchaseCheck = Purchase::where('supplier_id', $supplier->id)
                    ->where('merchant_id', Auth::user()->merchant->id)->first();
                if ($purchaseCheck) {
                    return ApiResponse::failure('Supplier data exists with purchase', Response::HTTP_CONFLICT);
                }

                $supplier->delete();

                return ApiResponse::success('Supplier deleted successfully.', Response::HTTP_OK);
            }

            return ApiResponse::success('supplier deleted successfully.', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('supplier not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function duePayment(DuePaymentRequest $request): JsonResponse
    {
        try {
            $merchantId = auth()->user()->merchant->id;

            // Check if the merchant has made a request in the last minute
            $cacheKey = 'last_due_payment_' . $merchantId . '_supplier_id_' . $request->supplier_id . '_from_account_id_' . $request->from_account_id . '_amount_' . $request->amount;
            if (Cache::has($cacheKey)) {
                $lastRequestTime = Cache::get($cacheKey);
                $currentTime     = now();

                if ($currentTime->diffInSeconds($lastRequestTime) < 60) {
                    return ApiResponse::failure('You can only make one payment request per minute', Response::HTTP_FORBIDDEN);
                }
            }

            // Validate input data
            $data = $request->validated();

            // Check if the "from account" has enough balance
            $fromAccount = Wallet::findOrFail($data['from_account_id']);
            if ($fromAccount->available_balance < $data['amount']) {
                return ApiResponse::failure('Insufficient balance in the from account.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::beginTransaction();

            $fromAccount->decrement('available_balance', $data['amount']);

            $supplier = Supplier::findOrFail($data['supplier_id']);

            $supplier->increment('balance', $data['amount']);

            // create a transaction
            $transaction = Transaction::create([
                'merchant_id'      => $merchantId,
                'transaction_type' => 'supplier-due-payment',
                'type'             => Transaction::$TYPE_WITHDRAW,
                'amount'           => $request->amount,
                'note'             => $request->description,
                'ref_no'           => $request->reference,
            ]);

            $transaction->attachment = $request->attachment;
            $transaction->save();

            SupplierPayment::create([
                'merchant_id'     => $merchantId,
                'supplier_id'     => $request->supplier_id,
                'from_account_id' => $request->from_account_id,
                'transaction_id'  => $transaction->id,
                'amount'          => $request->amount,
            ]);

            DB::commit();

            // Update the last request time in cache
            Cache::put($cacheKey, now(), 60);

            return ApiResponse::successMessageForCreate('Transaction Created Successfully', $transaction, Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            $errors = $v->validator->errors();

            return ApiResponse::validationError('There were validation errors.', $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Resource not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function supplierPurchases(Request $request, int $supplier_id): JsonResponse
    {
        try {
            $merchantId = auth()->user()->merchant->id;

            $perPage = (int) $request->query('per_page', 10);

            $purchases = Purchase::where('merchant_id', $merchantId)
                ->where('supplier_id', $supplier_id)
                ->select(
                    'id',
                    'purchase_invoice_no',
                    'total_item',
                    'grand_total',
                    'purchase_status_id',
                    'purchase_date'
                )
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            return ApiResponse::formatPagination(
                'Purchases retrieved successfully',
                $purchases,
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function supplierTransaction(Request $request, int $supplier_id): JsonResponse
    {
        try {

            $merchantId = auth()->user()->merchant->id;
            $perPage    = (int) $request->query('per_page', 10);

            $transactions = SupplierPurchasePaymentDetail::query()
                ->with([
                    'account:id,name',
                    'supplierPurchasePayment' => function ($query) use ($merchantId, $supplier_id) {
                        $query->where('merchant_id', $merchantId)
                            ->where('supplier_id', $supplier_id)
                            ->where('paid_status', '!=', "full_due")
                            ->select(
                                'id',
                                'purchase_id',
                                'paid_amount',
                            )
                            ->with(['purchase:id,purchase_invoice_no']);
                    },
                ])
                ->whereHas('supplierPurchasePayment', function ($query) use ($merchantId, $supplier_id) {
                    $query->where('merchant_id', $merchantId)
                        ->where('supplier_id', $supplier_id);
                })
                ->orderByDesc('date')
                ->paginate(perPage: 10);


            $transactions->setCollection(
                $transactions->getCollection()->map(function ($detail) {
                    return [
                        'id'                  => $detail->id,
                        'account_name'        => $detail->account?->name,
                        'current_paid_amount' => $detail->current_paid_amount,
                        'date'                => $detail->date,
                        'purchase_invoice_no' => $detail->supplierPurchasePayment?->purchase?->purchase_invoice_no,
                    ];
                })
            );
            return ApiResponse::formatPagination(
                'Supplier transactions retrieved successfully',
                $transactions,
                Response::HTTP_OK
            );
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
