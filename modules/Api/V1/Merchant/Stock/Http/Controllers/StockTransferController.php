<?php

namespace Modules\Api\V1\Merchant\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Stock\Http\Requests\StockTransferRequest;
use App\Models\Product\Product;
use App\Models\Stock\StockInventory;
use App\Models\Stock\StockOrder;
use App\Models\Stock\StockTransfer;
use App\Models\Stock\StockTransferDetail;
use App\Models\Warehouse\Warehouse;
use App\Services\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class StockTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-stock-transfers')->only('index', 'show');
        $this->middleware('shop.permission:create-stock-transfer')->only('store');
        $this->middleware('shop.permission:search-stock-transfer-products')->only('search');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $search = trim((string) $request->input('search', ''));

        $stockTransfers = StockTransfer::with('stockTransferDetails.product', 'stockTransferDetails.productVariation')
            ->where('merchant_id', auth()->user()->merchant->id)
            ->when($request->filled('from_warehouse_id'), function ($query) use ($request) {
                $query->where('from_warehouse_id', $request->from_warehouse_id);
            })
            ->when($request->filled('to_warehouse_id'), function ($query) use ($request) {
                $query->where('to_warehouse_id', $request->to_warehouse_id);
            })
            ->when($request->filled('start_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->end_date);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'LIKE', "%{$search}%")
                        ->orWhere('note', 'LIKE', "%{$search}%");
                    if (is_numeric($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            })

            ->orderBy('id', 'desc')->paginate($perPage);

        $stockTransfersData = $stockTransfers->getCollection()->map(function ($stockTransfer) {

            return [
                'id'             => $stockTransfer->id,
                'reference'      => $stockTransfer->reference,
                'note'           => $stockTransfer->note,
                'shipping_cost'  => $stockTransfer->shipping_cost,
                'from_warehouse' => [
                    'id'   => $stockTransfer->fromWarehouse->id,
                    'name' => $stockTransfer->fromWarehouse->name,
                ],
                'to_warehouse' => [
                    'id'   => $stockTransfer->toWarehouse->id,
                    'name' => $stockTransfer->toWarehouse->name,
                ],
                'products' => $stockTransfer->stockTransferDetails->map(function ($detail) {

                    $product     = $detail->product;
                    $isVariation = (bool) $detail->productVariation;

                    return [
                        'id'           => $product->id,
                        'name'         => $product->name,
                        'sku'          => $isVariation ? $detail->productVariation->sku : $product->sku,
                        'is_variation' => $isVariation,
                        'quantity'     => $detail->qty,
                    ];
                }),
                'created_at' => $stockTransfer->created_at->format('Y-m-d h:i A'),
            ];
        });

        $paginatedResults = new LengthAwarePaginator($stockTransfersData, $stockTransfers->total(), $stockTransfers->perPage(), $stockTransfers->currentPage(), ['path' => $stockTransfers->path()]);

        return ApiResponse::formatPagination('Stock transfers fetched successfully.', $paginatedResults, Response::HTTP_OK);
    }

    public function show(int $id)
    {
        $stockTransfer = StockTransfer::with('stockTransferDetails.product', 'stockTransferDetails.productVariation')
            ->where('merchant_id', auth()->user()->merchant->id)
            ->find($id);

        if (! $stockTransfer) {
            return ApiResponse::error('Stock Transfer not found', Response::HTTP_NOT_FOUND);
        }

        $formattedTransfer = [
            'id'             => $stockTransfer->id,
            'reference'      => $stockTransfer->reference,
            'note'           => $stockTransfer->note,
            'shipping_cost'  => $stockTransfer->shipping_cost,
            'from_warehouse' => [
                'id'      => $stockTransfer->fromWarehouse->id,
                'name'    => $stockTransfer->fromWarehouse->name,
                'phone'   => $stockTransfer->fromWarehouse->phone,
                'address' => $stockTransfer->fromWarehouse->address,
            ],
            'to_warehouse' => [
                'id'      => $stockTransfer->toWarehouse->id,
                'name'    => $stockTransfer->toWarehouse->name,
                'phone'   => $stockTransfer->toWarehouse->phone,
                'address' => $stockTransfer->toWarehouse->address,
            ],
            'products' => $stockTransfer->stockTransferDetails->map(function ($detail) {
                $product     = $detail->product;
                $isVariation = (bool) $detail->productVariation;

                return [
                    'id'           => $product->id,
                    'name'         => $product->name,
                    'sku'          => $isVariation ? $detail->productVariation->sku : $product->sku,
                    'is_variation' => $isVariation,
                    'quantity'     => $detail->qty,
                    'image'        => $product->image,
                ];
            }),
            'created_at' => $stockTransfer->created_at->format('Y-m-d H:i:A'),
        ];

        return ApiResponse::success('Stock Transfer details fetched successfully.', $formattedTransfer, Response::HTTP_OK);
    }

    public function store(StockTransferRequest $request)
    {
        $validated = $request->validated();

        $fromWarehouseId = $validated['from_warehouse_id'];

        $toWarehouseId = $validated['to_warehouse_id'];

        // check warehouse status
        $toWarehouse = Warehouse::where(['id' => $toWarehouseId, 'merchant_id' => auth()->user()->merchant->id, 'status' => 1])->first();

        if (! $toWarehouse) {
            return ApiResponse::error('To warehouse is not active', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            $stockTransfer = StockTransfer::create([
                'merchant_id'       => auth()->user()->merchant->id,
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id'   => $toWarehouseId,
                'shipping_cost'     => $validated['shipping_cost'],
                'reference'         => $validated['reference'],
                'note'              => $validated['note'],
            ]);

            foreach ($validated['products'] as $product) {
                StockTransferDetail::create([
                    'stock_transfer_id'    => $stockTransfer->id,
                    'product_id'           => $product['product_id'],
                    'product_variation_id' => $product['product_variation_id'],
                    'qty'                  => $product['qty'],
                ]);

                $availableStock = StockInventory::where(function ($query) use ($product) {
                    $query->where('product_variation_id', $product['product_variation_id'])
                        ->whereNotNull('product_variation_id')
                        ->orWhere('product_id', $product['product_id']);
                })->whereNotNull('purchase_id')

                    ->whereHas('stockOrders', function ($query) use ($fromWarehouseId) {
                        $query->where('warehouse_id', $fromWarehouseId)->whereNull('sell_product_detail_id');
                    })
                    ->withCount('stockOrders')
                    ->orderBy('id', 'asc')
                    ->get();

                $availableStockQty = $availableStock->sum('stock_orders_count');

                if ($availableStockQty < $product['qty']) {
                    throw new \Exception('Not enough stock available');
                }

                $remainingQtyToUpdate = $product['qty'];

                foreach ($availableStock as $stockInventory) {

                    $stockOrders = $stockInventory->stockOrders()
                        ->where('warehouse_id', $fromWarehouseId)
                        ->whereNull('sell_product_detail_id')
                        ->orderBy('id', 'asc')
                        ->get();

                    foreach ($stockOrders as $stockOrder) {

                        if ($remainingQtyToUpdate > 0) {

                            $stockOrder->warehouse_id = $toWarehouseId;

                            $stockOrder->save();

                            $remainingQtyToUpdate--;
                        }

                        if ($remainingQtyToUpdate <= 0) {
                            break;
                        }
                    }

                    if ($remainingQtyToUpdate <= 0) {
                        break;
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error('Stock Transfer creation failed : ' . $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return ApiResponse::success('Stock Transfer created successfully', $stockTransfer, Response::HTTP_CREATED);
    }

    public function search(Request $request)
    {
        if (empty($request->warehouse_id)) {
            return ApiResponse::failure('warehouse_id is required', Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $warehouseId = $request->warehouse_id;

        $products = Product::with(['productDetail'])
            ->with(['variations' => function ($query) use ($request, $warehouseId) {
                if ($request->search) {
                    $searchTerm = "%{$request->search}%";
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('sku', 'LIKE', $searchTerm)
                            ->orWhereHas('product', function ($pq) use ($searchTerm) {
                                $pq->where('name', 'LIKE', $searchTerm);
                            });
                    });
                }
                $query->whereHas('stockInventories', function ($si) use ($warehouseId) {
                    $si->whereNotNull('purchase_id')
                        ->whereHas('stockOrders', function ($so) use ($warehouseId) {
                            $so->where('warehouse_id', $warehouseId)
                                ->whereNull('sell_product_detail_id');
                        });
                });
            }])
            ->where('merchant_id', auth()->user()->merchant->id)
            ->where(function ($query) use ($request) {
                $searchTerm = "%{$request->search}%";
                $query->where('name', 'LIKE', $searchTerm)
                    ->orWhere('sku', 'LIKE', $searchTerm)
                    ->orWhereHas('variations', function ($variationQuery) use ($searchTerm) {
                        $variationQuery->where('sku', 'LIKE', $searchTerm);
                    });
            });

        $products->whereHas('stockInventories', function ($si) use ($warehouseId) {
            $si->whereNotNull('purchase_id')
                ->whereHas('stockOrders', function ($so) use ($warehouseId) {
                    $so->where('warehouse_id', $warehouseId)
                        ->whereNull('sell_product_detail_id');
                });
        });

        $products = $products->get();

        $results = $products->flatMap(function ($product) use ($warehouseId) {
            if ($product->product_type_id == Product::$PRODUCT_TYPE_SINGLE) {

                $availableStock = StockOrder::where('warehouse_id', $warehouseId)->whereHas('stockInventory', function ($query) use ($product) {
                    $query->where(['product_id' => $product->id])->whereNotNull('purchase_id');
                })->whereNull('sell_product_detail_id');

                $stockQty = $availableStock->count();

                return [
                    [
                        'id'              => $product->id,
                        'name'            => $product->name,
                        'image'           => $product->image[0] ?? null,
                        'sku'             => $product->sku,
                        'product_type'    => 'single',
                        'regular_price'   => $product->productDetail->regular_price,
                        'purchase_price'  => $product->productDetail->purchase_price,
                        'discount_price'  => $product->productDetail->discount_price,
                        'wholesale_price' => $product->productDetail->wholesale_price,
                        'stock_qty'       => $stockQty,
                        'variation_id'    => null,
                        'attributes'      => null,
                    ],
                ];
            }

            if ($product->product_type_id == Product::$PRODUCT_TYPE_VARIANT) {
                return $product->variations->map(function ($variation) use ($product, $warehouseId) {
                    $availableStock = StockOrder::where('warehouse_id', $warehouseId)->whereHas('stockInventory', function ($query) use ($product, $variation) {
                        $query->where(['product_id' => $product->id, 'product_variation_id' => $variation->id])->whereNotNull('purchase_id');
                    })->whereNull('sell_product_detail_id');

                    $stockQty = $availableStock->count();

                    return [
                        'id'              => $product->id,
                        'name'            => $product->name,
                        'image'           => $variation->image ?? ($product->image[0] ?? null),
                        'sku'             => $variation->sku,
                        'product_type'    => 'variant',
                        'regular_price'   => $variation->regular_price,
                        'purchase_price'  => $variation->purchase_price,
                        'discount_price'  => $variation->discount_price,
                        'wholesale_price' => $variation->wholesale_price,
                        'stock_qty'       => $stockQty,
                        'variation_id'    => $variation->id,
                        'attributes'      => $variation->variationAttributes->map(function ($variationAttribute) {
                            return [
                                'attribute_id'          => $variationAttribute->attribute_id,
                                'attribute_name'        => $variationAttribute->attribute->name,
                                'attribute_option_id'   => $variationAttribute->attribute_option_id,
                                'attribute_option_name' => $variationAttribute->attributeOption->attribute_value,
                            ];
                        }),
                    ];
                });
            }

            return [];
        });

        if ($results->isEmpty()) {
            return ApiResponse::customFailure('No products found', $results, Response::HTTP_OK);
        }

        return ApiResponse::success('Product found successfully.', $results, Response::HTTP_OK);
    }
}
