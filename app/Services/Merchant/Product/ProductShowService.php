<?php

namespace App\Services\Merchant\Product;

use App\Models\Product\Product;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductShowService
{
    public function showProduct(string $slug, $merchantId): JsonResponse
    {
        try {
            $product            = $this->fetchProduct($slug, $merchantId);
            $transformedProduct = $this->formatProduct($product);

            return ApiResponse::success('Product retrieved successfully', $transformedProduct, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Product not found', Response::HTTP_NOT_FOUND);
        }
    }

    protected function fetchProduct(string $slug, $merchantId): Product
    {
        return Product::with('media')
            ->where(['slug' => $slug, 'merchant_id' => $merchantId])
            ->with(['productDetail', 'category', 'subCategory', 'subCategoryChild', 'brand', 'unit'])
            ->firstOrFail();
    }

    protected function formatProduct(Product $product): array
    {
        $productDetail           = $product->productDetail;
        $productCategory         = $product->category;
        $productSubCategory      = $product->subCategory;
        $productSubCategoryChild = $product->subCategoryChild;
        $productBrand            = $product->brand;
        $productUnit             = $product->unit;

        return [
            'id'              => $product->id,
            'name'            => $product->name,
            'slug'            => $product->slug,
            'description'     => $product->description,
            'specification'   => $product->specification,
            'sku'             => $product->sku,
            'product_type_id' => $product->product_type_id,
            'barcode'         => $product->barcode,
            'weight'          => $product->weight,
            'category'        => [
                'id'   => $productCategory->id   ?? null,
                'name' => $productCategory->name ?? null,
            ],
            'sub_category' => $productSubCategory
                ? [
                    'id'   => $productSubCategory->id,
                    'name' => $productSubCategory->name,
                ]
                : [],
            'sub_category_child' => $productSubCategoryChild
                ? [
                    'id'   => $productSubCategoryChild->id,
                    'name' => $productSubCategoryChild->name,
                ]
                : [],
            'brand' => $productBrand
                ? [
                    'id'   => $productBrand->id,
                    'name' => $productBrand->name,
                ]
                : [],
            'unit' => $productUnit
                ? [
                    'id'   => $productUnit->id,
                    'name' => $productUnit->name,
                ]
                : [],
            'details' => $productDetail
                ? [
                    'purchase_price'  => $productDetail->purchase_price,
                    'regular_price'   => $productDetail->regular_price,
                    'discount_price'  => $productDetail->discount_price,
                    'wholesale_price' => $productDetail->wholesale_price,
                    'minimum_qty'     => $productDetail->minimum_qty,
                    'selling_type_id' => $productDetail->selling_type_id,
                    'accounting'      => [
                        'purchase_account_id'  => $productDetail->purchase_account_id,
                        'inventory_account_id' => $productDetail->inventory_account_id,
                        'sale_account_id'      => $productDetail->sale_account_id,
                    ],
                ]
                : [],
            'images'          => $product->image,
            'thumbnail'       => $product->getFirstUrl('thumbnail'),
            'warranty_note'   => $product->warranty_note ?? null,
            'has_warranty'    => $product->has_warranty ? 1 : 0,
            'warranty_type'   => json_decode($product->warranty_type),
            'created_at'      => $product->created_at,
            'updated_at'      => $product->updated_at,
            'total_stock_qty' => $product->total_stock_qty,
        ];
    }
}
