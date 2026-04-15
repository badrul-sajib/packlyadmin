<?php

namespace Modules\Api\V1\Ecommerce\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Order\Http\Resources\AllOrderListResource;
use Modules\Api\V1\Ecommerce\Order\Http\Resources\BuyAgainItemResource;
use App\Http\Resources\Ecommerce\OrderDetails;
use Modules\Api\V1\Ecommerce\Order\Http\Resources\OrderItemResource;
use Modules\Api\V1\Ecommerce\Order\Http\Resources\OrderShopResource;
use Modules\Api\V1\Ecommerce\Order\Http\Resources\OrderToPayResource;
use App\Models\Order\OrderItem;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function shopOrders(Request $request): JsonResponse
    {
        $orders = $this->orderService->getCustomerOrder($request);

        return resourceFormatPagination('Orders fetched successfully', OrderShopResource::collection($orders->items()), $orders);
    }

    public function orderCounts(): JsonResponse
    {
        $counts = $this->orderService->getCustomerOrderCounts();

        return success('Order counts fetched successfully', $counts);
    }

    public function orderCancelDetails($id): JsonResponse
    {
        return $this->orderService->getCancelDetails($id);
    }

    public function buyAgain($orderId): JsonResponse
    {
        return success('Orders fetched successfully', BuyAgainItemResource::collection($this->orderService->getBuyAgainItems(id: $orderId)));

    }

    public function toPayOrders(Request $request): JsonResponse
    {
        $orders = $this->orderService->getToPayOrders($request);

        return resourceFormatPagination('To pay orders fetched successfully', OrderToPayResource::collection($orders->items()), $orders);
    }

    public function toPayOrderDetails($orderId): JsonResponse
    {
        try {
            $order = $this->orderService->getToPayOrderDetails($orderId);

            return success('To pay order details fetched successfully', new OrderToPayResource($order));
        } catch (ModelNotFoundException $m) {
            return failure($m->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function orderDetails(string $trackingId)
    {
        try {
            $orderDetails =  $this->orderService->getCustomerOrderDetails($trackingId);

            return success('Order details fetched successfully', new OrderDetails($orderDetails));
        } catch (ModelNotFoundException $th) {
            return failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function orderItem(OrderItem $orderItem): JsonResponse
    {
        try {
            if ($orderItem->merchant->order->user_id != auth()->id()) {
                return failure('Invalid order', Response::HTTP_NOT_FOUND);
            }

            return success('Order item fetched successfully', new OrderItemResource($orderItem));
        } catch (\Throwable $th) {
            return failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function allOrderList(Request $request): JsonResponse
    {
        $orders = $this->orderService->getAllOrders($request);

        return resourceFormatPagination('Orders fetched successfully', AllOrderListResource::collection($orders->items()), $orders);
    }
}
