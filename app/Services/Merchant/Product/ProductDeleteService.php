<?php

namespace App\Services\Merchant\Product;

use App\Enums\OrderStatus;
use App\Enums\ShopProductStatus;
use App\Models\Product\Product;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ProductDeleteService
{
    public function deleteProduct(Product $product): JsonResponse
    {
        try {
            $status = $product->shopProduct()->where('status', ShopProductStatus::APPROVED->value)->first();

            if ($status) {
                return ApiResponse::failure('Please disable the product first from E-commerce.', Response::HTTP_CONFLICT);
            }

            $cancelStatusIds = [
                OrderStatus::CANCELLED->value,
            ];

            $hasActiveOrders = $product->orderItems()
                ->whereNotIn('status_id', $cancelStatusIds)
                ->exists();

            if ($hasActiveOrders) {
                return ApiResponse::failure('Product has active order items. Cannot delete.', Response::HTTP_CONFLICT);
            }

            DB::beginTransaction();

            $product->variations()->update(['status' => '0']);

            $product->update(['status' => '0']);

            $product->delete();
            DB::commit();

            return ApiResponse::success('Product deleted successfully.', [], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::failure('Product not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
