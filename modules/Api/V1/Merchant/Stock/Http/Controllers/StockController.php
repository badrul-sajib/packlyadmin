<?php

namespace Modules\Api\V1\Merchant\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Stock\StockOrder;
use App\Models\Warehouse\Warehouse;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-stock-summary')->only('index');
    }
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->with([
                'productDetail',
                'stockInventories.stockOrders',
                'variations.stockInventories.stockOrders',
            ])
            ->where('merchant_id', auth()->user()->merchant->id);

        $this->applyFilters($query, $request);

        $products = $query->orderBy('created_at', 'DESC')->get();

        $results = $this->transformProducts($products, $request);

        if ($results->isEmpty()) {
            return ApiResponse::failure('No results found', Response::HTTP_NOT_FOUND);
        }

        $paginatedResults = $this->paginateResults($results, $request);

        return ApiResponse::formatPagination('Stock Summary fetched successfully.', $paginatedResults, Response::HTTP_OK);
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->sub_category_id) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->warehouse_id) {
            $query->whereHas('variations', function ($q) use ($request) {
                $q->whereHas('stockInventories.stockOrders', function ($sq) use ($request) {
                    $sq->where('warehouse_id', $request->warehouse_id)
                        ->whereNull('sell_product_detail_id');
                });
            });
        }

        if ($request->search) {
            $searchTerm = "%{$request->search}%";
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                    ->orWhere('sku', 'LIKE', $searchTerm)
                    ->orWhereHas('variations', function ($vq) use ($searchTerm) {
                        $vq->where('sku', 'LIKE', $searchTerm);
                    });
            });
        }
    }

    private function getStockCounts($stockable)
    {
        $stockInventories = $stockable->stockInventories;

        $openingStock = $stockInventories
            ->where('purchase_id', null)
            ->sum('stock_qty');

        $stockIn = $stockInventories->sum('stock_qty');

        $stockOut = $stockInventories->sum(function ($inventory) {

            return $inventory->stockOrders
                ->whereNotNull('sell_product_detail_id')
                ->count();
        });

        return [
            'opening_stock' => (int) $openingStock,
            'stock_in'      => (int) $stockIn,
            'stock_out'     => (int) $stockOut,
            'closing_stock' => (int) ($stockIn - $stockOut),
        ];
    }

    private function transformProducts($products, Request $request)
    {
        return $products->flatMap(function ($product) use ($request) {
            if ($product->product_type_id == Product::$PRODUCT_TYPE_SINGLE) {
                return $this->transformSingleProduct($product, $request);
            }

            if ($product->product_type_id == Product::$PRODUCT_TYPE_VARIANT) {
                return $this->transformVariantProduct($product, $request);
            }

            return [];
        })->values();
    }

    private function getWarehouseInfo($warehouseId, $productId)
    {
        if (! $warehouseId) {
            return null;
        }

        $warehouse = Warehouse::find($warehouseId);

        $availableStock = StockOrder::where('warehouse_id', $warehouseId)
            ->whereHas('stockInventory', function ($query) use ($productId) {
                $query->where('product_id', $productId)
                    ->whereNotNull('purchase_id');
            })
            ->whereNull('sell_product_detail_id')
            ->count();

        return [
            'id'              => $warehouseId,
            'name'            => $warehouse ? $warehouse->name : '',
            'available_stock' => (int) $availableStock,
        ];
    }

    private function paginateResults($results, Request $request)
    {
        $perPage     = $request->per_page ?? 10;
        $currentPage = $request->page     ?? 1;
        $offset      = ($currentPage - 1) * $perPage;

        return new LengthAwarePaginator(
            $results->slice($offset, $perPage)->values(),
            $results->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    private function transformSingleProduct($product, Request $request)
    {
        $stockCounts = $this->getStockCounts($product);

        return [[
            'id'              => $product->id,
            'name'            => $product->name,
            'image'           => $product->thumbnail ?? null,
            'sku'             => $product->sku,
            'category_id'     => $product->category_id,
            'sub_category_id' => $product->sub_category_id,
            'brand_id'        => $product->brand_id,
            'product_type'    => 'single',
            'opening_stock'   => $stockCounts['opening_stock'],
            'stock_in'        => $stockCounts['stock_in'],
            'stock_out'       => $stockCounts['stock_out'],
            'closing_stock'   => $stockCounts['closing_stock'],
            'warehouse'       => $this->getWarehouseInfo($request->warehouse_id, $product->id),
        ]];
    }

    private function transformVariantProduct($product, Request $request)
    {
        return $product->variations->map(function ($variation) use ($product, $request) {

            $stockCounts = $this->getStockCounts($variation);

            return [
                'id'              => $product->id,
                'name'            => $product->name,
                'image'           => $variation->image ?? ($product->thumbnail ?? null),
                'sku'             => $variation->sku,
                'category_id'     => $product->category_id,
                'sub_category_id' => $product->sub_category_id,
                'brand_id'        => $product->brand_id,
                'product_type'    => 'variant',
                'opening_stock'   => $stockCounts['opening_stock'],
                'stock_in'        => $stockCounts['stock_in'],
                'stock_out'       => $stockCounts['stock_out'],
                'closing_stock'   => $stockCounts['closing_stock'],
                'warehouse'       => $this->getWarehouseInfo($request->warehouse_id, $product->id),
            ];
        })->values();
    }
}
