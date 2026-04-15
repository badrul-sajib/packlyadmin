<?php

namespace App\Services\Merchant\Product;

use App\Models\Product\Product;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductSearchService
{
    public function searchProducts(Request $request, $merchantId): JsonResponse
    {
        $products = $this->buildQuery($request, $merchantId)->get();

        $results = $this->formatResults($products);

        if ($results->isEmpty()) {
            return ApiResponse::customFailure('No products found', $results, Response::HTTP_OK);
        }

        return ApiResponse::success('Product found successfully.', $results, Response::HTTP_OK);
    }

    protected function buildQuery(Request $request, $merchantId): \Illuminate\Database\Eloquent\Builder
    {
        $query = Product::with(['productDetail'])
            ->with([
                'variations' => function ($query) use ($request) {
                    if ($request->search) {
                        $searchTerm = "%{$request->search}%";

                        if ($request->has('in_stock') && $request->in_stock == 1) {
                            $query->where('total_stock_qty', '>', 0);
                        }

                        $query->where(function ($q) use ($searchTerm) {
                            $q->where('sku', 'LIKE', $searchTerm)
                                ->orWhere('barcode', 'LIKE', $searchTerm)
                                ->orWhereHas('product', function ($pq) use ($searchTerm) {
                                    $pq->where('name', 'LIKE', $searchTerm);
                                });
                        });
                    }
                },
            ])
            ->where('merchant_id', $merchantId)
            ->where(function ($query) use ($request) {
                $searchTerm = "%{$request->search}%";

                if ($request->has('in_stock') && $request->in_stock == 1) {
                    $query->where('total_stock_qty', '>', 0);
                }

                $query
                    ->where('name', 'LIKE', $searchTerm)
                    ->orWhere('sku', 'LIKE', $searchTerm)
                    ->orWhere('barcode', 'LIKE', $searchTerm)
                    ->orWhereHas('variations', function ($variationQuery) use ($searchTerm) {
                        $variationQuery->where('sku', 'LIKE', $searchTerm)->orWhere('barcode', 'LIKE', $searchTerm);
                    });
            });

        if ($request->has('category') && !empty($request->category)) {
            $query->whereHas('category', function ($categoryQuery) use ($request) {
                $categoryQuery->where('id', $request->category);
            });
        }

        if ($request->has('brand') && !empty($request->brand)) {
            $query->where('brand_id', $request->brand);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        return $query;
    }

    protected function formatResults($products): \Illuminate\Support\Collection
    {
        return $products->flatMap(function ($product) {
            if ($product->product_type_id == Product::$PRODUCT_TYPE_SINGLE) {
                return [$this->formatSingleProduct($product)];
            }

            if ($product->product_type_id == Product::$PRODUCT_TYPE_VARIANT) {
                return $this->formatVariantProducts($product);
            }

            return [];
        });
    }

    protected function formatSingleProduct($product): array
    {
        $productDetail = $product->productDetail;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'image' => $product->image[0] ?? null,
            'sku' => $product->sku,
            'product_type' => 'single',
            'regular_price' => $productDetail->regular_price,
            'purchase_price' => $productDetail->purchase_price,
            'e_price' => $productDetail->e_price,
            'e_discount_price' => $productDetail->e_discount_price,
            'discount_price' => $productDetail->discount_price,
            'wholesale_price' => $productDetail->wholesale_price,
            'stock_qty' => $product->total_stock_qty,
            'variation_id' => null,
            'attributes' => null,
        ];
    }

    protected function formatVariantProducts($product): \Illuminate\Support\Collection
    {
        return $product->variations->map(function ($variation) use ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $variation->image ?? ($product->image[0] ?? null),
                'sku' => $variation->sku,
                'product_type' => 'variant',
                'regular_price' => $variation->regular_price,
                'purchase_price' => $variation->purchase_price,
                'e_price' => $variation->e_price,
                'e_discount_price' => $variation->e_discount_price,
                'discount_price' => $variation->discount_price,
                'wholesale_price' => $variation->wholesale_price,
                'stock_qty' => $variation->total_stock_qty,
                'variation_id' => $variation->id,
                'attributes' => $variation->variationAttributes->map(function ($variationAttribute) {
                    return [
                        'attribute_id' => $variationAttribute->attribute_id,
                        'attribute_name' => $variationAttribute->attribute->name,
                        'attribute_option_id' => $variationAttribute->attribute_option_id,
                        'attribute_option_name' => $variationAttribute->attributeOption->attribute_value,
                    ];
                }),
            ];
        });
    }
}
