<?php

namespace App\Services\Merchant\Product;

use App\Models\Product\Product;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ProductRestoreService
{
    public function fetchTrashedProducts(int $merchantId): JsonResponse
    {
        $perPage = request()->integer('per_page', 10);

        // Fetch products with filters
        $products = $this->queryProducts(request(), $merchantId, $perPage);

        // Format products
        $formattedProducts = ProductListService::formatProducts($products->getCollection());

        // Create paginated response
        $paginatedProducts = new LengthAwarePaginator(
            $formattedProducts,
            $products->total(),
            $products->perPage(),
            $products->currentPage(),
            ['path' => $products->path()]
        );

        return ApiResponse::formatPagination('Trashed products fetched successfully.', $paginatedProducts, Response::HTTP_OK);
    }

    public function restoreProduct(Product $product): JsonResponse
    {
        try {
            DB::beginTransaction();

            $product->restore();

            $product->variations()->update(['status' => '1']);

            $product->update(['status' => '1']);

            DB::commit();

            return ApiResponse::success('Product restored successfully.', [], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::failure('Product not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Product not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function hideFromTrash(Product $product): JsonResponse
    {
        try {
            $product->update(['is_hidden' => true]);

            return ApiResponse::success('Product deleted successfully.', [], Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Product not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function queryProducts(Request $request, $merchantId, $perPage): LengthAwarePaginator
    {
        return Product::onlyTrashed()
            ->where('is_hidden', false)
            ->where('merchant_id', $merchantId)
            ->with('category', 'subCategory', 'subCategoryChild', 'brand', 'unit', 'productDetail', 'addedByUser')
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query
                        ->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('sku', 'like', value: '%'.$request->search.'%')
                        ->orWhere('barcode', 'like', '%'.$request->search.'%');
                });
            })
            ->with(['variations' => function ($query) {
                $query->where('status', 1)
                    ->with(['variationAttributes.attribute.options']);
            }])
            ->when($request->has('category'), function ($query) use ($request) {
                $query->whereHas('category', function ($categoryQuery) use ($request) {
                    $categoryQuery->where('id', $request->category);
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
}
