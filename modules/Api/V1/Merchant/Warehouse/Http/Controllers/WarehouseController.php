<?php

namespace Modules\Api\V1\Merchant\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Warehouse\Http\Requests\WarehouseRequest;
use App\Models\Stock\StockOrder;
use App\Models\Warehouse\Warehouse;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-warehouses')->only('index', 'show', 'report');
        $this->middleware('shop.permission:create-warehouse')->only('store');
        $this->middleware('shop.permission:update-warehouse')->only('update');
        $this->middleware('shop.permission:change-warehouse-status')->only('status');
        $this->middleware('shop.permission:delete-warehouse')->only('destroy');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $warehouses = Warehouse::where('merchant_id', auth()->user()->merchant?->id)
                ->with('merchant')
                ->where(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $query->whereAny(['name', 'phone'], 'LIKE', "%$request->search%");
                    }
                })
                ->when($request->has('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->orderBy('id', 'desc')
                ->paginate($request->query('per_page', 10));

            return ApiResponse::formatPagination('warehouses retrieved successfully', $warehouses, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(WarehouseRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $validatedData['merchant_id'] = auth()->user()->merchant->id;

            $warehouse = Warehouse::create($validatedData);

            return ApiResponse::successMessageForCreate('Warehouse Created Successfully', $warehouse, Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $warehouse = Warehouse::where('merchant_id', auth()->user()->merchant->id)->findOrFail($id);

            return ApiResponse::success('show warehouse details', $warehouse);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Warehouse not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(WarehouseRequest $request, int $id): JsonResponse
    {
        try {
            $warehouse = Warehouse::where('merchant_id', auth()->user()->merchant->id)->findOrFail($id);

            $warehouse->update($request->validated());

            return ApiResponse::success('Warehouse Updated Successfully', $warehouse, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Warehouse not found.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return ApiResponse::validationError('There were validation errors.', $e->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function status(int $id): JsonResponse
    {
        try {
            $warehouse = Warehouse::where('merchant_id', auth()->user()->merchant->id)
                ->where('id', $id)
                ->firstOrFail();

            $warehouse->update(['status' => $warehouse->status == '1' ? '0' : '1']);

            return ApiResponse::success('Warehouse Status Updated Successfully', $warehouse, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Warehouse not found.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $warehouse = Warehouse::where('merchant_id', auth()->user()->merchant->id)->where('id', $id)->firstOrFail();

            if ($warehouse->stockOrders()->exists()) {
                return ApiResponse::failure('Cannot delete warehouse with associated stock orders.', Response::HTTP_CONFLICT);
            }

            if ($warehouse->purchases()->exists()) {
                return ApiResponse::failure('Cannot delete warehouse with associated purchases.', Response::HTTP_CONFLICT);
            }

            $warehouse->delete();

            return ApiResponse::success('Warehouse deleted successfully.', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Warehouse not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function report(Request $request, int $id): JsonResponse
    {
        try {
            $merchantId = auth()->user()->merchant->id;

            $warehouse = Warehouse::where('merchant_id', $merchantId)
                ->findOrFail($id);

            $stockOrders = $this->getFilteredStockOrders($warehouse->id, $request);

            return ApiResponse::formatPagination('Warehouse report', $stockOrders['data'], Response::HTTP_OK, $stockOrders['metadata']);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Warehouse not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure(
                'Failed to retrieve warehouse report',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    protected function getFilteredStockOrders(int $warehouseId, Request $request): array
    {
        $stockOrders = StockOrder::where('warehouse_id', $warehouseId)
            ->with(['stockInventory.product'])
            ->when($request->filled('start_date'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($query) use ($request) {
                $query->where('created_at', '<=', $request->end_date);
            })
            ->when($request->filled('product_id'), function ($query) use ($request) {
                $query->whereHas('stockInventory', function ($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $groupedOrders = $stockOrders->groupBy('stockInventory.product_id')->map(function ($orders, $productId) {
            $firstOrder = $orders->first();

            return [
                'product_id'      => $productId,
                'image'           => $firstOrder->stockInventory->product->thumbnail,
                'product_name'    => $firstOrder->stockInventory->product->name,
                'total_stock'     => $orders->count(),
                'sold_stock'      => $orders->whereNotNull('sell_product_detail_id')->count(),
                'available_stock' => $orders->whereNull('sell_product_detail_id')->count(),
                'stock_date'      => $firstOrder->created_at,
            ];
        })->values();

        $page    = $request->page     ?? 1;
        $perPage = $request->per_page ?? 15;
        $offset  = ($page - 1) * $perPage;

        $paginatedItems = array_slice($groupedOrders->toArray(), $offset, $perPage);

        $paginator = new LengthAwarePaginator(
            $paginatedItems,
            $groupedOrders->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return [
            'data'     => $paginator,
            'metadata' => [
                'warehouse_id'    => $warehouseId,
                'sold_stock'      => $stockOrders->whereNotNull('sell_product_detail_id')->count(),
                'available_stock' => $stockOrders->whereNull('sell_product_detail_id')->count(),
                'total_quantity'  => $stockOrders->count(),
            ],
        ];
    }
}
