<?php

namespace Modules\Api\V1\Ecommerce\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order\CartItem;
use App\Models\Order\OrderItem;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\VisitorTrackable;

class PublicEventController extends Controller
{
    use VisitorTrackable;
    public function index(Request $request): JsonResponse
    {
        try {
            if ($response = $this->handleVisitor($request)) {

                return $response;
            }
            $type = strtolower((string) $request->input('type', ''));
            $perPage = (int) ($request->input('per_page', 20));
            $productId = $request->input('product_id');
            $userId = $request->input('user_id');
            $publicPassword = env('PUBLIC_API_PASSWORD');
            $deviceId = env('DEVICE_ID');

            $headerAccessKey = $request->header('accessKey');
            $deviceKeyHeader = $request->header('deviceId');
            $signatureHeader = $request->header('signature');

            if (! $headerAccessKey || ! $deviceKeyHeader || ! $signatureHeader) {
                return ApiResponse::error('An error occurred while fetching events.', Response::HTTP_FORBIDDEN);
            }

            if ($deviceId !== $deviceKeyHeader) {
                return ApiResponse::error('An error occurred while fetching events.', Response::HTTP_FORBIDDEN);
            }

            $expectedSignature = md5($publicPassword . $deviceId);
            if (! hash_equals($expectedSignature, $signatureHeader)) {
                return ApiResponse::error('An error occurred while fetching events.', Response::HTTP_FORBIDDEN);
            }

            if ($headerAccessKey !== $publicPassword || $deviceKeyHeader !== $deviceId) {
                return ApiResponse::error('An error occurred while fetching events.', Response::HTTP_FORBIDDEN);
            }

            if (! in_array($type, ['cart', 'order'])) {
                return response()->json(['message' => 'Invalid type. Use cart or order_placed'], 422);
            }

            if ($type === 'cart') {
                $query = CartItem::query()
                    ->select([
                        'cart_items.product_id',
                        'cart_items.created_at',
                        'carts.user_id',
                        'products.merchant_id',
                    ])
                    ->join('carts', 'carts.id', '=', 'cart_items.cart_id')
                    ->join('products', 'products.id', '=', 'cart_items.product_id')
                    ->when($productId, fn($q) => $q->where('cart_items.product_id', $productId))
                    ->when($userId, fn($q) => $q->where('carts.user_id', $userId))
                    ->orderBy('cart_items.created_at', 'desc')
                    ->paginate($perPage);

                $items = $query->getCollection()->map(function ($row) {
                    return [
                        'user_id' => (int) $row->user_id,
                        'product_id' => (int) $row->product_id,
                        'merchant_id' => $row->merchant_id ? (int) $row->merchant_id : null,
                        'event_type' => 'add_to_cart',
                        'timestamp' => $row->created_at->toDateTimeString(),
                    ];
                });
            } else {
                $query = OrderItem::query()
                    ->select([
                        'order_items.product_id',
                        'order_items.created_at',
                        'orders.user_id',
                        'products.merchant_id',
                    ])
                    ->join('merchant_orders as mo', 'mo.id', '=', 'order_items.merchant_order_id')
                    ->join('orders', 'orders.id', '=', 'mo.order_id')
                    ->join('products', 'products.id', '=', 'order_items.product_id')
                    ->when($productId, fn($q) => $q->where('order_items.product_id', $productId))
                    ->when($userId, fn($q) => $q->where('orders.user_id', $userId))
                    ->orderBy('order_items.created_at', 'desc')
                    ->paginate($perPage);

                $items = $query->getCollection()->map(function ($row) {
                    return [
                        'user_id' => (int) $row->user_id,
                        'product_id' => (int) $row->product_id,
                        'merchant_id' => $row->merchant_id ? (int) $row->merchant_id : null,
                        'event_type' => 'order',
                        'timestamp' => $row->created_at->toDateTimeString(),
                    ];
                });
            }

            return response()->json([
                'message' => 'Events fetched successfully',
                'data' => $items,
                'total' => $query->total(),
                'last_page' => $query->lastPage(),
                'current_page' => $query->currentPage(),
                'next_page_url' => $query->nextPageUrl(),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
