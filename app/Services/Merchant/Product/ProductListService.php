<?php

namespace App\Services\Merchant\Product;

use App\Models\Product\Product;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class ProductListService
{
    public function fetchProducts(Request $request, $merchantId): JsonResponse
    {
        $perPage = $request->input('per_page', 10);

        // Fetch products with filters
        $products = $this->queryProducts($request, $merchantId, $perPage);

        // Format products
        $formattedProducts = $this->formatProducts($products->getCollection());

        // Create paginated response
        $paginatedProducts = new LengthAwarePaginator(
            $formattedProducts,
            $products->total(),
            $products->perPage(),
            $products->currentPage(),
            ['path' => $products->path()]
        );
        $counts = $this->getStatusCounts($merchantId);
        return ApiResponse::formatPaginationWithCounts('Products fetched successfully.', $paginatedProducts, $counts);
    }

    protected function queryProducts(Request $request, $merchantId, $perPage): LengthAwarePaginator
    {
        return Product::where('merchant_id', $merchantId)
            ->with('category', 'subCategory', 'subCategoryChild', 'brand', 'unit', 'productDetail.selectedVariation:id,product_id', 'addedByUser')
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query
                        ->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('sku', 'like', '%' . $request->search . '%')
                        ->orWhere('barcode', 'like', '%' . $request->search . '%');
                });
            })
            ->with([
                'variations' => function ($query) {
                    $query->where('status', 1)
                        ->with(['variationAttributes.attribute.options']);
                }
            ])
            ->when($request->has('in_stock'), function ($query) {
                if (request()->in_stock == 1) {
                    $query->where('total_stock_qty', '>', 0);
                } elseif (request()->in_stock == 0) {
                    $query->where('total_stock_qty', '=', 0);
                }
            })
            ->when($request->has('category'), function ($query) use ($request) {
                $query->whereHas('category', function ($categoryQuery) use ($request) {
                    $categoryQuery->where('id', $request->category);
                });
            })
            ->when($request->has('brand'), function ($query) use ($request) {
                $query->whereHas('brand', function ($brandQuery) use ($request) {
                    $brandQuery->where('id', $request->brand);
                });
            })
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->has('type'), function ($query) use ($request) {
                $query->where('product_type_id', $request->type);
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public static function formatProducts($products): \Illuminate\Support\Collection
    {
        return $products->map(function ($product) {
            $productDetail = $product->productDetail;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'sku' => $product->sku,
                'category' => $product->category ? $product->category->name : null,
                'sub_category' => $product->subCategory ? $product->subCategory->name : null,
                'sub_category_child' => $product->subCategoryChild ? $product->subCategoryChild->name : null,
                'brand' => $product->brand ? $product->brand->name : null,
                'unit' => $product->unit ? $product->unit->name : null,
                'product_type' => $product->product_type_id == 1 ? 'single' : 'variant',
                'purchase_price' => (int) $productDetail?->purchase_price,
                'stock_qty' => (int) $product->total_stock_qty,
                'status' => (int) $product->status,
                'added_by' => $product->added_by ? $product->addedByUser->name : null,
                'product_detail' => [
                    'id' => $productDetail?->id,
                    'is_enable_accounting' => $productDetail?->is_enable_accounting == 1 ? 'ON' : 'OFF',
                    'selling_type' => match ($productDetail?->selling_type_id) {
                        1 => 'Retail',
                        2 => 'Wholesale',
                        3 => 'Both',
                        default => 'Unknown',
                    },
                    'price' => (int) $productDetail?->price,
                    'discount_price' => (int) $productDetail?->discount_price,
                    'wholesale_price' => (int) $productDetail?->wholesale_price,
                    'regular_price' => (int) $productDetail?->regular_price,
                    'minimum_qty' => $productDetail?->minimum_qty,
                    'stock_qty' => $productDetail?->stock_qty,
                    'purchase_account_id' => $productDetail?->purchase_account_id,
                    'inventory_account_id' => $productDetail?->inventory_account_id,
                    'sale_account_id' => $productDetail?->sale_account_id,
                ],
                'images' => $product->image,
                'thumbnail' => $product->thumbnail ?: self::getDefaultVariantImage($product),
                'on_shop' => $product->shopProduct ? true : false,
                'created_at' => $product->created_at?->format('Y/m/d H:i'),
                'updated_at' => $product->updated_at?->format('Y/m/d H:i'),
            ];
        });
    }

    public static function getDefaultVariantImage(Product $product)
    {
        return $product->productDetail->selectedVariation?->image ?? null;
    }
    protected function getStatusCounts($merchantId): array
    {
        $counts = Product::withTrashed()
            ->where('merchant_id', $merchantId)
            ->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN deleted_at IS NULL AND status = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN deleted_at IS NULL AND status = 0 THEN 1 ELSE 0 END) as inactive,
            SUM(CASE WHEN deleted_at IS NOT NULL THEN 1 ELSE 0 END) as draft
        ')
            ->first();

        return [
            'total' => (int) $counts->total,
            'active' => (int) $counts->active,
            'inactive' => (int) $counts->inactive,
            'draft' => (int) $counts->draft,
        ];
    }

}
