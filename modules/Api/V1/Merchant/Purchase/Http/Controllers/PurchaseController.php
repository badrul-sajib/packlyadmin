<?php

namespace Modules\Api\V1\Merchant\Purchase\Http\Controllers;

use App\Enums\AccountTypes;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Purchase\Http\Requests\PurchaseRequest;
use Modules\Api\V1\Merchant\Purchase\Http\Requests\PurchaseStatusChangeRequest;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\PurchaseDetail;
use App\Models\Stock\StockInventory;
use App\Models\Stock\StockOrder;
use App\Models\Supplier\Supplier;
use App\Models\Supplier\SupplierPurchasePayment;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-purchases')->only('index', 'show');
        $this->middleware('shop.permission:create-purchase')->only('store');
        $this->middleware('shop.permission:update-purchase')->only('update', 'purchaseStatusChange');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);

            $purchases = Purchase::with([
                'supplier:id,name',
                'warehouse:id,name',
                'purchaseDetails.product:id,name',
                'supplierPurchasePayment:id,purchase_id,paid_status,paid_amount,due_amount,total_amount,note',
                'supplierPurchasePayment.supplierPurchasePaymentDetails' => function ($query) {
                    $query->select('id', 'supplier_purchase_payment_id', 'account_id', 'current_paid_amount', 'date', 'note', 'reference')
                        ->with('account:id,account_type,name')
                        ->orderBy('id', 'desc');
                },
            ])
                ->where('merchant_id', auth()->user()->merchant->id)
                ->when($request->supplier_id, function ($query) use ($request) {
                    $query->where('supplier_id', $request->supplier_id);
                })
                ->when($request->warehouse_id, function ($query) use ($request) {
                    $query->where('warehouse_id', $request->warehouse_id);
                })
                ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                    $query->whereBetween('purchase_date', [$request->start_date, $request->end_date]);
                })
                ->when($request->has('payment_status'), function ($query) use ($request) {
                    $query->where('payment_status_id', $request->payment_status);
                })
                ->when($request->has('search') && ! empty($request->search), function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('ref_no', 'like', "%{$request->search}%")
                            ->orWhere('note', 'like', "%{$request->search}%")
                            ->orWhereHas('supplier', function ($q) use ($request) {
                                $q->where('name', 'like', "%{$request->search}%");
                            })
                            ->orWhereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'like', "%{$request->search}%");
                            });
                    });
                });

            $purchases = $purchases->orderBy('id', 'desc')->paginate($perPage);

            return ApiResponse::formatPagination('Purchase list retrieved successfully', $purchases, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_BAD_REQUEST);
        }
    }

    public function store(PurchaseRequest $request)
    {
        $merchantId = $request->user()->merchant->id;

        try {
            $request->validated();

            // check purchase status exists
            DB::beginTransaction();

            $totalItem             = 0;
            $totalAmount           = 0;
            $productDiscountAmount = 0;
            $productVatAmount      = 0;
            $subTotal              = 0;

            $purchase = Purchase::create([
                'merchant_id'         => $merchantId,
                'supplier_id'         => $request->supplier_id,
                'warehouse_id'        => $request->warehouse_id,
                'ref_no'              => $request->ref_no,
                'note'                => $request->note,
                'purchase_status_id'  => $request->purchase_status_id,
                'purchase_invoice_no' => $this->generateUniqueInvoiceNo(),
                'payment_status_id'   => Purchase::$PAYMENT_STATUS_DUE,
                'purchase_date'       => $request->purchase_date,
            ]);

            if ($request->hasFile('attachment')) {
                $purchase->addMedia($request->file('attachment'), 'attachment');
            }

            // $products = $request->products;
            $products = json_decode($request->products, true);

            foreach ($products as $product) {
                if (! Product::where(['id' => $product['product_id'], 'merchant_id' => $merchantId])->first()) {
                    return ApiResponse::failure('Product not found');
                }

                if ($product['purchase_qty'] > 1000) {
                    return ApiResponse::failure('Purchase quantity cannot be greater than 1000');
                }

                $totalItem += $product['purchase_qty'];
                $singleTotalAmount = $product['purchase_price'] * $product['purchase_qty'];
                $totalAmount += $singleTotalAmount;
                $singleProductDiscountAmount = discountCalculation($product['purchase_price'], $product['product_discount_percentage']) * $product['purchase_qty'];
                $productDiscountAmount += $singleProductDiscountAmount;
                $singleProductVatAmount = taxCalculation($singleTotalAmount - $singleProductDiscountAmount, $product['product_vat']);
                $productVatAmount += $singleProductVatAmount;
                $singleSubTotal = $singleTotalAmount - $singleProductDiscountAmount + $singleProductVatAmount;
                $subTotal += $singleSubTotal;

                PurchaseDetail::create([
                    'purchase_id'                 => $purchase->id,
                    'product_id'                  => $product['product_id'],
                    'variation_id'                => $product['product_variation_id'] ?? null,
                    'purchase_qty'                => $product['purchase_qty'],
                    'unit_cost'                   => $product['purchase_price'],
                    'product_discount_percentage' => $product['product_discount_percentage'],
                    'product_vat_percentage'      => $product['product_vat'],
                    'sub_total'                   => $singleSubTotal,
                ]);

                $stockInventory = StockInventory::create([
                    'purchase_id'          => $purchase->id,
                    'merchant_id'          => $merchantId,
                    'product_variation_id' => $product['product_variation_id'],
                    'purchase_price'       => $product['purchase_price'],
                    'product_id'           => $product['product_id'],
                    'regular_price'        => $product['regular_price'],
                    'stock_qty'            => $product['purchase_qty'],
                ]);

                // product stock update
                $productData = Product::findOrFail($product['product_id']);

                if (empty($product['product_variation_id'])) {
                    $canUpdatePrice = ($product['purchase_price'] && $product['regular_price']) != 0;

                    if (! $canUpdatePrice) {
                        $product = Product::where('merchant_id', $merchantId)->find($product['product_id']);

                        throw ValidationException::withMessages([
                            'products' => ["Product '$product->name': The purchase price or regular price can't be less than the existing price."],
                        ]);
                    }

                    if ($canUpdatePrice) {
                        $productData->productDetail->update([
                            'purchase_price'  => $product['purchase_price'],
                            'regular_price'   => $product['regular_price'],
                            'discount_price'  => $product['discount_price'],
                            'wholesale_price' => $product['wholesale_price'],
                        ]);
                    }
                } else {
                    $productVariation = ProductVariation::find($product['product_variation_id']);

                    $canUpdatePrice = $productVariation->purchase_price <= $product['purchase_price'] && $productVariation->regular_price <= $product['regular_price'];

                    if (! $canUpdatePrice) {
                        $product = Product::where('merchant_id', $merchantId)->find($product['product_id']);

                        throw ValidationException::withMessages([
                            'products' => ["Product '$product->name': The purchase price or regular price can't be less than the existing price."],
                        ]);
                    }

                    if ($canUpdatePrice) {
                        $productVariation->purchase_price  = $product['purchase_price'];
                        $productVariation->regular_price   = $product['regular_price'];
                        $productVariation->discount_price  = $product['discount_price'];
                        $productVariation->wholesale_price = $product['wholesale_price'];
                        $productVariation->save();
                    }

                    if ($productVariation) {
                        $productVariation->increment('total_stock_qty', $product['purchase_qty']);
                    }
                }

                $productData->increment('total_stock_qty', $product['purchase_qty']);

                $stockOrders = [];

                for ($i = 0; $i < $product['purchase_qty']; $i++) {
                    $stockOrders[] = [
                        'uuid'               => (string) Str::uuid(),
                        'stock_inventory_id' => $stockInventory->id,
                        'warehouse_id'       => $request->warehouse_id,
                        'purchase_price'     => $product['purchase_price'],
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];
                }

                // insert stock orders
                StockOrder::insert($stockOrders);
            }

            $totalDiscountAmount = discountCalculation($subTotal, $request->total_discount_percentage);
            $totalPurchaseVat    = taxCalculation($subTotal - $totalDiscountAmount, $request->total_purchase_vat_percentage);

            $grandTotal = $subTotal - $totalDiscountAmount + $totalPurchaseVat + $request->total_shipping_cost;

            if ($grandTotal < $request->current_paid_amount) {
                DB::rollBack();

                return ApiResponse::error('Current paid amount can not be greater than grand total');
            }

            $purchase->update([
                'total_item'                    => $totalItem,
                'total_discount_percentage'     => $request->total_discount_percentage,
                'total_discount_amount'         => $totalDiscountAmount,
                'total_purchase_vat_percentage' => $request->total_purchase_vat_percentage,
                'total_purchase_vat_amount'     => $totalPurchaseVat,
                'total_shipping_cost'           => $request->total_shipping_cost,
                'total_amount'                  => $totalAmount,
                'sub_total'                     => $subTotal,
                'grand_total'                   => $grandTotal,
                'total_items_discount'          => $productDiscountAmount,
                'total_items_vat'               => $productVatAmount,
                'paid_amount'                   => 0,
                'due_amount'                    => $grandTotal,
                'return_amount'                 => $request->return_amount ?? 0,
            ]);

            $purchase->payments()->create([
                'account_id' => null,
                'amount'     => 0,
            ]);

            $paymentStatus = SupplierPurchasePayment::$FULL_DUE;

            $currentPaidAmount = $request->current_paid_amount ?? 0;
            $initialDueAmount  = $grandTotal - $currentPaidAmount;
            if ($request->has('current_paid_amount')) {
                if ($request->has('current_paid_amount') && $request->current_paid_amount > 0) {
                    $account = Account::where('id', $request->account_id)->where('merchant_id', $merchantId)->first();
                    if (! $account || $account->balance < $currentPaidAmount) {
                        return ApiResponse::failure('Insufficient wallet balance', Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
                }

                if ($currentPaidAmount > $grandTotal) {
                    return ApiResponse::failure('Payment amount cannot exceed order amount of ' . $grandTotal, Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                if ($currentPaidAmount == 0) {
                    $paymentStatus   = SupplierPurchasePayment::$FULL_DUE;
                    $paymentStatusId = Purchase::$PAYMENT_STATUS_DUE;
                } elseif ($currentPaidAmount == $grandTotal) {
                    $paymentStatus   = SupplierPurchasePayment::$FULL_PAID;
                    $paymentStatusId = Purchase::$PAYMENT_STATUS_PAID;
                } else {
                    $paymentStatus   = SupplierPurchasePayment::$PARTIAL_PAID;
                    $paymentStatusId = Purchase::$PAYMENT_STATUS_PARTIAL;
                }
            } else {
                $paymentStatus = SupplierPurchasePayment::$FULL_DUE;
            }

            $supplierPurchasePayment = $purchase->supplierPurchasePayment()->create([
                'supplier_id'  => $request->supplier_id,
                'merchant_id'  => $merchantId,
                'paid_status'  => $paymentStatus,
                'paid_amount'  => $currentPaidAmount,
                'due_amount'   => $initialDueAmount,
                'total_amount' => $grandTotal,
            ]);

            $purchase->update([
                'payment_status_id' => $paymentStatusId,
                'due_amount'        => $initialDueAmount,
                'paid_amount'       => $currentPaidAmount,
            ]);

            $uuid = Str::uuid();

            // Fetch Accounts
            $inventoryLedger = Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::INVENTORY->value, 'uucode' => 'INAS'])->first();
            $accountsPayable = Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::LIABILITIES->value, 'uucode' => 'ACPA'])->first();
            $revenue         = Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::INCOME->value, 'uucode' => 'REVE'])->first();
            $vatAndTax       = Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::EXPENSE->value, 'uucode' => 'VATT'])->first();
            $deliveryCost    = Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::EXPENSE->value, 'uucode' => 'DELC'])->first();
            $purchasesLedger = Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::PURCHASE->value, 'uucode' => 'INPU'])->first();

            $inventoryLedger->increment('balance', $totalAmount);

            MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $inventoryLedger->id,
                'amount'      => $totalAmount,
                'date'        => $request->payment_date ?? now(),
                'type'        => 'debit',
                'reason'      => 'Purchase Product Increased Inventory Assets (Purchase)',
            ]);

            $accountsPayable->increment('balance', $totalAmount);

            MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $accountsPayable->id,
                'amount'      => $totalAmount,
                'date'        => $request->payment_date ?? now(),
                'type'        => 'credit',
                'reason'      => 'Accounts Payable Increased with Product Price (Purchase)',
            ]);

            if ($productDiscountAmount > 0) {
                $accountsPayable->decrement('balance', $productDiscountAmount);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $accountsPayable->id,
                    'amount'      => -$productDiscountAmount,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'debit',
                    'reason'      => 'Accounts Payable Decreased with Product Discount (Purchase)',
                ]);

                $revenue->increment('balance', $productDiscountAmount);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $revenue->id,
                    'amount'      => $productDiscountAmount,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'credit',
                    'reason'      => 'Purchase Product Discount Applied (Purchase)',
                ]);
            }

            if ($productVatAmount > 0) {
                $accountsPayable->increment('balance', $productVatAmount);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $accountsPayable->id,
                    'amount'      => $productVatAmount,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'credit',
                    'reason'      => 'Accounts Payable Increased with Product Vat (Purchase)',
                ]);

                $vatAndTax->increment('balance', $productVatAmount);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $vatAndTax->id,
                    'amount'      => $productVatAmount,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'debit',
                    'reason'      => 'Purchase Product Tax Applied (Purchase)',
                ]);
            }

            if ($totalDiscountAmount > 0) {
                $accountsPayable->decrement('balance', $totalDiscountAmount);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $accountsPayable->id,
                    'amount'      => -$totalDiscountAmount,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'debit',
                    'reason'      => 'Accounts Payable Decreased with Sales Discount (Purchase)',
                ]);

                $revenue->increment('balance', $totalDiscountAmount);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $revenue->id,
                    'amount'      => $totalDiscountAmount,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'credit',
                    'reason'      => 'Purchase Sales Discount Applied (Purchase)',
                ]);
            }

            if ($totalPurchaseVat > 0) {
                $accountsPayable->increment('balance', $totalPurchaseVat);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $accountsPayable->id,
                    'amount'      => $totalPurchaseVat,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'credit',
                    'reason'      => 'Accounts Payable Increased with Sales Vat (Purchase)',
                ]);

                $vatAndTax->increment('balance', $totalPurchaseVat);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $vatAndTax->id,
                    'amount'      => $totalPurchaseVat,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'debit',
                    'reason'      => 'Purchase Sales Tax Applied (Purchase)',
                ]);
            }

            if ($request->total_shipping_cost > 0) {
                $accountsPayable->increment('balance', $request->total_shipping_cost);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $accountsPayable->id,
                    'amount'      => $request->total_shipping_cost,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'credit',
                    'reason'      => 'Accounts Payable Increased with Shipping Cost (Purchase)',
                ]);

                $deliveryCost->increment('balance', $request->total_shipping_cost);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $deliveryCost->id,
                    'amount'      => $request->total_shipping_cost,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'debit',
                    'reason'      => 'Purchase Shipping Cost (Purchase)',
                ]);
            }

            $purchasesLedger->increment('balance', $grandTotal);

            MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $purchasesLedger->id,
                'amount'      => $grandTotal,
                'date'        => $request->payment_date ?? now(),
                'type'        => 'debit',
                'reason'      => 'Purchase Product Increase Inventory',
            ]);

            // 5. Record Purchase (Total Cost Including VAT & Shipping)

            // 6. Payment Handling (If Partial Payment is Done)
            if ($request->has('current_paid_amount') && $request->current_paid_amount > 0) {
                $supplierPurchasePayment->supplierPurchasePaymentDetails()->create([
                    'account_id'          => $request->account_id,
                    'current_paid_amount' => $request->current_paid_amount,
                    'date'                => $request->payment_date ?? now(),
                ]);

                // Reduce Accounts Payable
                $accountsPayable->decrement('balance', $request->current_paid_amount);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $accountsPayable->id,
                    'amount'      => -$request->current_paid_amount,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'debit',
                    'reason'      => 'Accounts Payable Reduced (Supplier Payment)',
                ]);

                $account->decrement('balance', $request->current_paid_amount);

                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $account->id,
                    'amount'      => -$request->current_paid_amount,
                    'date'        => $request->payment_date ?? now(),
                    'type'        => 'credit',
                    'reason'      => 'Cash/Bank Payment for Purchase',
                ]);
            }

            $supplier = Supplier::findOrFail($request->supplier_id);
            $supplier->increment('balance', $initialDueAmount);

            DB::commit();

            return ApiResponse::success('Purchase created successfully', Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $purchases = Purchase::with([
            'supplier',
            'warehouse',
            'purchaseDetails.product.productDetail',
            'purchaseDetails.productVariation',
            'stockInventory.stockOrders',
            'supplierPurchasePayment:id,purchase_id,paid_status,paid_amount,due_amount,total_amount,note',
            'supplierPurchasePayment.supplierPurchasePaymentDetails' => function ($query) {
                $query->select('id', 'supplier_purchase_payment_id', 'account_id', 'current_paid_amount', 'date', 'note', 'reference')
                    ->with('account:id,account_type,name')
                    ->orderBy('id', 'desc');
            },
        ])
            ->where('merchant_id', auth()->user()->merchant->id)
            ->where('id', $id)
            ->where('id', $id)
            ->first();
        return ApiResponse::success('Purchase detail retrieved successfully', $purchases, Response::HTTP_OK);
    }

    public function purchaseStatusChange(PurchaseStatusChangeRequest $request, int $id)
    {
        try {
            $purchase = Purchase::where('merchant_id', auth()->user()->merchant->id)->findOrFail($id);

            $request->validated();

            if ($purchase->purchase_status_id == 2) {
                return ApiResponse::failure('You cannot change the status to ordered if it is already received.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $purchase->update(['purchase_status_id' => $request->purchase_status_id]);

            return ApiResponse::success('Purchase status updated successfully.', $purchase, Response::HTTP_OK);
        } catch (ModelNotFoundException $m) {
            return ApiResponse::failure('purchase info not found', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors.', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $merchantId = auth()->user()->merchant->id;

        $validator = Validator::make($request->all(), [
            'supplier_id' => [
                'required',
                'integer',
                Rule::exists('suppliers', 'id')->where(function ($query) use ($merchantId) {
                    $query->where('merchant_id', $merchantId);
                }),
            ],
            'warehouse_id' => [
                'required',
                'integer',
                Rule::exists('warehouses', 'id')->where(function ($query) use ($merchantId) {
                    $query->where('merchant_id', $merchantId);
                }),
            ],
            'account_id' => [
                'integer',
                Rule::exists('accounts', 'id')
                    ->where(function ($query) use ($merchantId) {
                        $query->where('merchant_id', $merchantId);
                    })
                    ->when(request('current_paid_amount') > 0, function () {
                        return ['required'];
                    }),
            ],
            'ref_no'                        => 'nullable|string',
            'note'                          => 'nullable|string|max:255',
            'purchase_status_id'            => 'nullable|integer',
            'purchase_date'                 => 'nullable|date',
            'attachment'                    => 'nullable|file|mimes:jpeg,png,pdf',
            'total_item'                    => 'nullable|integer',
            'total_discount_percentage'     => 'nullable|numeric',
            'total_discount_amount'         => 'nullable|numeric',
            'total_purchase_vat_percentage' => 'nullable|integer',
            'total_purchase_vat_amount'     => 'nullable|numeric',
            'total_shipping_cost'           => 'nullable|numeric',
            'total_amount'                  => 'nullable|numeric',
            'sub_total'                     => 'nullable|numeric',
            'grand_total'                   => 'nullable|numeric',
            'purchase_qty'                  => 'nullable|integer',
            'products'                      => 'required|json',
            'current_paid_amount'           => 'nullable|numeric|min:0',
            'payment_date'                  => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return ApiResponse::validationError('Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $checkSold = Purchase::find($id)
            ->stockInventory()
            ->whereHas('stockOrders', function ($query) {
                $query->whereNotNull('sell_product_detail_id');
            })
            ->exists();

        if ($checkSold) {
            return ApiResponse::failure('You can not update this purchase. One of the products is already sold from this purchase', Response::HTTP_CONFLICT);
        }

        try {
            DB::beginTransaction();

            $purchase = Purchase::where('merchant_id', $merchantId)->findOrFail($id);

            if ($purchase->purchase_status_id == 2 && $request->purchase_status_id == 1) {
                return ApiResponse::failure('You cannot change the status to ordered if it is already received.', Response::HTTP_CONFLICT);
            }

            $purchase->update([
                'supplier_id'        => $request->supplier_id,
                'warehouse_id'       => $request->warehouse_id,
                'ref_no'             => $request->ref_no,
                'note'               => $request->note,
                'purchase_status_id' => $request->purchase_status_id,
                'purchase_date'      => $request->purchase_date,
            ]);

            if ($request->hasFile('attachment')) {
                $purchase->addMedia($request->file('attachment'), 'attachment');
            }

            $totalItem             = 0;
            $totalAmount           = 0;
            $productDiscountAmount = 0;
            $productVatAmount      = 0;
            $subTotal              = 0;

            $products = json_decode($request->products, true);

            $purchase->stockInventory()->each(function ($stockInventory) {
                $stockInventory->stockOrders()->delete();
            });

            $purchase->stockInventory()->delete();

            foreach ($products as $product) {

                if ($product['purchase_qty'] > 1000) {
                    return ApiResponse::failure('Purchase quantity cannot be greater than 1000', Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $purchaseDetail = $purchase
                    ->purchaseDetails()
                    ->where(['purchase_id' => $purchase->id, 'product_id' => $product['product_id'], 'variation_id' => $product['product_variation_id']])
                    ->first();

                $previousStock = $purchaseDetail->purchase_qty;

                $purchaseDetail->delete();

                $totalItem += $product['purchase_qty'];
                $singleTotalAmount = $product['purchase_price'] * $product['purchase_qty'];
                $totalAmount += $singleTotalAmount;
                $singleProductDiscountAmount = discountCalculation($product['purchase_price'], $product['product_discount_percentage']) * $product['purchase_qty'];
                $productDiscountAmount += $singleProductDiscountAmount;
                $singleProductVatAmount = taxCalculation($singleTotalAmount - $singleProductDiscountAmount, $product['product_vat']);
                $productVatAmount += $singleProductVatAmount;
                $singleSubTotal = $singleTotalAmount - $singleProductDiscountAmount + $singleProductVatAmount;
                $subTotal += $singleSubTotal;

                PurchaseDetail::create([
                    'purchase_id'                 => $purchase->id,
                    'product_id'                  => $product['product_id'],
                    'variation_id'                => $product['product_variation_id'] ?? null,
                    'purchase_qty'                => $product['purchase_qty'],
                    'unit_cost'                   => $product['purchase_price'],
                    'product_discount_percentage' => $product['product_discount_percentage'],
                    'product_vat_percentage'      => $product['product_vat'],
                    'sub_total'                   => $singleSubTotal,
                ]);

                $stockInventory = StockInventory::create([
                    'purchase_id'          => $purchase->id,
                    'merchant_id'          => $merchantId,
                    'product_variation_id' => $product['product_variation_id'],
                    'purchase_price'       => $product['purchase_price'],
                    'wholesale_price'      => $product['wholesale_price'],
                    'product_id'           => $product['product_id'],
                    'regular_price'        => $product['regular_price'],
                    'stock_qty'            => $product['purchase_qty'],
                ]);

                // product stock update
                $productData = Product::with('productDetail')->findOrFail($product['product_id']);

                $productVariation = ProductVariation::find($product['product_variation_id']);

                if (empty($product['product_variation_id'])) {
                    $canUpdatePrice = $productData->productDetail->purchase_price <= $product['purchase_price'] && $productData->productDetail->regular_price <= $product['regular_price'];

                    if (! $canUpdatePrice) {
                        $product = Product::where('merchant_id', $merchantId)->find($product['product_id']);

                        throw ValidationException::withMessages([
                            'products' => ["Product '$product->name': The purchase price or regular price can't be less than the existing price."],
                        ]);
                    }

                    if ($canUpdatePrice) {
                        $productData->productDetail->update([
                            'purchase_price'  => $product['purchase_price']    ?? 0,
                            'regular_price'   => $product['regular_price']     ?? 0,
                            'discount_price'  => $product['discount_price']    ?? 0,
                            'wholesale_price' => $product['wholesale_price']   ?? 0,
                        ]);
                    }
                } else {
                    $canUpdatePrice = $productVariation->purchase_price <= $product['purchase_price'] && $productVariation->regular_price <= $product['regular_price'];

                    if (! $canUpdatePrice) {
                        $product = Product::where('merchant_id', $merchantId)->find($product['product_id']);

                        throw ValidationException::withMessages([
                            'products' => ["Product '$product->name': The purchase price or regular price can't be less than the existing price."],
                        ]);
                    }

                    if ($canUpdatePrice) {
                        $productVariation->purchase_price  = $product['purchase_price']  ?? 0;
                        $productVariation->regular_price   = $product['regular_price']   ?? 0;
                        $productVariation->discount_price  = $product['discount_price']  ?? 0;
                        $productVariation->wholesale_price = $product['wholesale_price'] ?? 0;
                        $productVariation->save();
                    }

                    $stockToIncrease = $product['purchase_qty'] - $previousStock;

                    $productVariation->increment('total_stock_qty', $stockToIncrease);
                }

                $stockOrders = [];

                for ($i = 0; $i < $product['purchase_qty']; $i++) {
                    $stockOrders[] = [
                        'uuid'               => (string) Str::uuid(),
                        'stock_inventory_id' => $stockInventory->id,
                        'purchase_price'     => $product['purchase_price'] ?? 0,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];
                }

                // insert stock orders
                StockOrder::insert($stockOrders);

                $stockToIncrease = $product['purchase_qty'] - $previousStock;

                Log::info($stockToIncrease);

                $productData->increment('total_stock_qty', $stockToIncrease);
            }

            $supplier = Supplier::find($purchase->supplier_id);

            $totalDiscountAmount = discountCalculation($subTotal, $request->total_discount_percentage);
            $totalPurchaseVat    = taxCalculation($subTotal - $totalDiscountAmount, $request->total_purchase_vat_percentage);

            $grandTotal = $subTotal - $totalDiscountAmount + $totalPurchaseVat + $request->total_shipping_cost;

            if ($supplier) {
                $previousGrandTotal = $purchase->grand_total;
                $supplier->update(['balance' => $supplier->balance - ($grandTotal - $previousGrandTotal)]);
            }

            $purchase->update([
                'total_item'                    => $totalItem,
                'total_discount_percentage'     => $request->total_discount_percentage,
                'total_discount_amount'         => $totalDiscountAmount,
                'total_purchase_vat_percentage' => $request->total_purchase_vat_percentage,
                'total_purchase_vat_amount'     => $totalPurchaseVat,
                'total_shipping_cost'           => $request->total_shipping_cost,
                'total_amount'                  => $totalAmount,
                'sub_total'                     => $subTotal,
                'grand_total'                   => $grandTotal,
                'total_items_discount'          => $productDiscountAmount,
                'total_items_vat'               => $productVatAmount,
                'paid_amount'                   => 0,
                'due_amount'                    => $grandTotal,
                'return_amount'                 => $request->return_amount ?? 0,
            ]);

            DB::commit();

            return ApiResponse::success('Purchase updated successfully', Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong', Response::HTTP_BAD_REQUEST);
        }
    }

    public function generateUniqueInvoiceNo(): int
    {
        do {
            $invoiceNo = random_int(10000000, 99999999);

            $exists = DB::table('purchases')->where('purchase_invoice_no', $invoiceNo)->exists();
        } while ($exists);

        return $invoiceNo;
    }
}
