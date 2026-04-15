<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Merchant\MerchantOrder;
use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public static function getOrders($request): LengthAwarePaginator
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $shippingType = $request->input('ship_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $orderFrom = $request->input('order_from');

        $query = Order::query()
            ->notSpam()
            ->with([
                'merchantOrders:id,order_id',
                'merchantOrders.payment:id,merchant_order_id,payment_method',
            ])
            ->withCount('orderItems')
            ->when($orderFrom, function ($q) use ($orderFrom) {
                return $q->where('order_from', $orderFrom);
            });
        if ($search) {
            $query->whereAny(
                ['invoice_id', 'customer_number'],
                'like',
                "%{$search}%"
            );
        } else {
            $query->when(
                $shippingType,
                fn($q) => $q->where('shipping_type', $shippingType)
            );

            $query->when(
                $startDate && $endDate,
                fn($q) => $q->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ])
            );
        }

        return $query
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public static function getOrderByInvoiceId($id)
    {
        return Order::where('invoice_id', $id)
            ->with([
                'merchantOrders',
                'merchantOrders.merchant',
                'merchantOrders.orderItems.product_variant.variationAttributes.attribute',
                'merchantOrders.orderItems.product_variant.variationAttributes.attributeOption',
                'merchantOrders.orderItems.product',
                'customer_location.parent.parent',
            ])
            ->first();
    }

    public function getCancelDetails($id): JsonResponse
    {
        $order = OrderItem::where('id', $id)->where('status_id', OrderStatus::CANCELLED->value)->first();
        if (!$order) {
            return failure('Order not found');
        }

        return success('Order cancel details fetched successfully', [
            'id' => $order->id,
            'tracking_id' => $order->merchant->tracking_id,
            'status' => $order->status_label,
            'cancelled_by' => $order->action_by ? 'Merchant' : 'Customer',
            'cancel_reason' => $order->itemCase?->reason?->name ?? '',
            'created_at' => $order->created_at->format('M d, Y'),
            'shop_id' => intval($order->merchant->merchant->id),
            'shop_name' => $order->merchant->merchant->shop_name,
            'shop_image' => $order->merchant->merchant->shop_logo ?? null,
            'product_name' => $order->product->name,
            'product_slug' => $order->product->slug,
            'product_thumbnail' => $order->product->thumbnail,
            'product_variant' => self::getOrderItemVariantText($order->product_variant->variations ?? []),
            'quantity' => $order->quantity,
            'price' => $order->price,
            'total_amount' => ($order->price * $order->quantity),
            'cancel_date' => $order?->itemCase?->created_at->format('M d, Y') ?? '',
        ]);
    }

    // -------------Api service-------------------#

    public function getCustomerOrder($request)
    {
        $user = userInfo();
        $status = $request->status ?? '';
        $orStatus = '';
        $perPage = $request->input('per_page', 10);

        if ($status == OrderStatus::PROCESSING->value) {
            $orStatus = OrderStatus::READY_TO_SHIP->value;
        }

        $allStatus = array_filter([$status, $orStatus], fn($value) => !empty($value));

        if (empty($allStatus)) {
            $allStatus = [
                OrderStatus::PENDING->value,
                OrderStatus::APPROVED->value,
                OrderStatus::PROCESSING->value,
                OrderStatus::READY_TO_SHIP->value,
                OrderStatus::DELIVERED->value,
                OrderStatus::CANCELLED->value,
            ];
        }

        return MerchantOrder::query()
            ->with('order')
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->when($allStatus, function ($query) use ($allStatus): void {
                $query->whereIn('status_id', $allStatus);
            })

            ->where(function ($query) {
                $query->where('status_id', '!=', OrderStatus::PENDING->value)
                    ->orWhere(function ($q) {
                        $q->where('status_id', OrderStatus::PENDING->value)
                            ->whereHas('payment', function ($subQuery) {
                                $subQuery->where(function ($q2) {
                                    $q2->where('payment_method', PaymentMethod::SSLCOMMERZ->value)
                                        ->where('payment_status', PaymentStatus::PAID->value);
                                })
                                    ->orWhere(function ($q2) {
                                        $q2->where('payment_method', PaymentMethod::COD->value);
                                    });
                            });
                    });
            })
            ->with([
                'merchant:id,name,shop_name',
                'payment',
                'orderItems:id,merchant_order_id,product_id,product_variation_id,price,quantity,status_id',
                'orderItems.product:id,name,slug,total_stock_qty,sku',
                'orderItems.product.reviews',
                'orderItems.product.media',
                'orderItems.product_variant.media',
                'orderItems.product_variant',
                'orderItems.product_variant.variations',
                'orderItems.product_variant.variations.attributeOption',
                'orderItems.product_variant.variations.attribute',
            ])
            ->select('id', 'tracking_id', 'order_id', 'total_amount', 'shipping_amount', 'merchant_id', 'status_id', 'sub_total', 'total_items', 'grand_total')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    public function getCustomerOrderCounts(): array
    {
        $user = userInfo();

        $counts = MerchantOrder::query()
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where(function ($query) {
                $query->where('status_id', '!=', OrderStatus::PENDING->value)
                    ->orWhere(function ($q) {
                        $q->where('status_id', OrderStatus::PENDING->value)
                            ->whereHas('payment', function ($subQuery) {
                                $subQuery->where(function ($q2) {
                                    $q2->where('payment_method', PaymentMethod::SSLCOMMERZ->value)
                                        ->where('payment_status', PaymentStatus::PAID->value);
                                })
                                    ->orWhere(function ($q2) {
                                        $q2->where('payment_method', PaymentMethod::COD->value);
                                    });
                            });
                    });
            })
            ->selectRaw('status_id, COUNT(*) as count')
            ->groupBy('status_id')
            ->pluck('count', 'status_id')
            ->toArray();

        $toPayCount = MerchantOrder::query()
            ->whereHas('order', fn($q) => $q->where('user_id', $user->id))
            ->where('status_id', OrderStatus::PENDING->value)
            ->whereHas('payment', function ($q) {
                $q->where('payment_method', PaymentMethod::SSLCOMMERZ->value)
                    ->whereNotIn('payment_status', [PaymentStatus::PAID->value, PaymentStatus::CANCELLED->value]);
            })
            ->distinct('order_id')
            ->count();

        return [
            // old way of count
            'pending' => $counts[OrderStatus::PENDING->value] ?? 0,
            'processing' => ($counts[OrderStatus::PROCESSING->value] ?? 0) + ($counts[OrderStatus::READY_TO_SHIP->value] ?? 0),
            'delivered' => $counts[OrderStatus::DELIVERED->value] ?? 0,
            'returned' => $counts[OrderStatus::RETURNED->value] ?? 0,
            'cancelled' => $counts[OrderStatus::CANCELLED->value] ?? 0,
            'total' => array_sum($counts) + $toPayCount,
            // new way of count
            'to_accept' => $counts[OrderStatus::PENDING->value] ?? 0,
            'to_ship' => ($counts[OrderStatus::APPROVED->value] ?? 0),
            'to_receive' => ($counts[OrderStatus::PROCESSING->value] ?? 0) + ($counts[OrderStatus::READY_TO_SHIP->value] ?? 0),
            'to_review' => $counts[OrderStatus::DELIVERED->value] ?? 0,
            'to_pay' => $toPayCount,

        ];
    }

    public function getCustomerOrderDetails(string $id) // tracking id is string
    {
        $merchantOrder = MerchantOrder::query()
            ->with([
                'merchant:id,name,shop_name',
            ])
            ->withCount('orderItems')
            ->where('tracking_id', $id)
            ->first();
        if (!$merchantOrder) {
            throw new ModelNotFoundException('Order not found');
        }

        return $merchantOrder;
    }

    public function getAllOrders($request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $user = userInfo();

        return Order::query()
            ->where('user_id', $user->id)
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['invoice_id', 'customer_number'], 'like', "%{$search}%");
            })
            ->with([
                'merchantOrders',
                'merchantOrders.merchant:id,name,shop_name,slug',
                'merchantOrders.payment',
                'merchantOrders.orderItems:id,merchant_order_id,product_id,product_variation_id,price,quantity,status_id',
                'merchantOrders.orderItems.product:id,name,slug,total_stock_qty,sku',
                'merchantOrders.orderItems.product.reviews',
                'merchantOrders.orderItems.product.media',
                'merchantOrders.orderItems.product_variant.media',
                'merchantOrders.orderItems.product_variant',
                'merchantOrders.orderItems.product_variant.variations',
                'merchantOrders.orderItems.product_variant.variations.attributeOption',
                'merchantOrders.orderItems.product_variant.variations.attribute',
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    // -------------Api service helper -------------------#

    public static function getOrderItemVariantText($variations = []): ?string
    {
        if (empty($variations)) {
            return null;
        }

        $text = '';
        foreach ($variations as $variation) {
            $text .= $variation->attribute->name . ': ' . $variation->attributeOption->attribute_value . ', ';
        }

        return rtrim($text, ', ');
    }

    public function getBuyAgainItems(int $id): array|Collection
    {
        $merchantOrder = MerchantOrder::where('tracking_id', $id)->first();

        return $merchantOrder->orderItems()->with([
            'product',
            'product.productDetail',
            'product.media',
            'product_variant',
            'product_variant.variationAttributes.attribute',
            'product_variant.variationAttributes.attributeOption',
        ])->get();
    }

    public function getToPayOrders($request)
    {
        $user = userInfo();
        $perPage = $request->input('per_page', 10);

        return Order::query()
            ->where('user_id', $user->id)
            ->whereHas('merchantOrders', function ($query) {
                $query->whereHas('payment', function ($subQuery) {
                    $subQuery->where('payment_method', PaymentMethod::SSLCOMMERZ->value)
                        ->whereNotIn('payment_status', [PaymentStatus::PAID->value, PaymentStatus::CANCELLED->value]);
                });
            })
            ->with([
                'merchantOrders' => function ($query) {
                    $query->whereHas('payment', function ($subQuery) {
                        $subQuery->where('payment_method', PaymentMethod::SSLCOMMERZ->value)
                            ->whereNotIn('payment_status', [PaymentStatus::PAID->value, PaymentStatus::CANCELLED->value]);
                    })->select('id', 'tracking_id', 'order_id', 'total_amount', 'shipping_amount', 'merchant_id', 'status_id', 'sub_total', 'total_items');
                },
                'merchantOrders.merchant:id,name,shop_name,slug',
                'merchantOrders.payment',
                'merchantOrders.orderItems:id,merchant_order_id,product_id,product_variation_id,price,quantity,status_id',
                'merchantOrders.orderItems.product:id,name,slug,total_stock_qty,sku',
                'merchantOrders.orderItems.product.reviews',
                'merchantOrders.orderItems.product.media',
                'merchantOrders.orderItems.product_variant.media',
                'merchantOrders.orderItems.product_variant',
                'merchantOrders.orderItems.product_variant.variations',
                'merchantOrders.orderItems.product_variant.variations.attributeOption',
                'merchantOrders.orderItems.product_variant.variations.attribute',
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    public function getToPayOrderDetails($orderId)
    {
        $user = userInfo();
        $exists = Order::where('user_id', $user->id)->where('id', $orderId)->exists();

        if (!$exists) {
            throw new ModelNotFoundException('Order not found');
        }

        return Order::query()
            ->where('id', $orderId)
            ->with([
                'merchantOrders:id,tracking_id,order_id,total_amount,shipping_amount,merchant_id,status_id,sub_total,total_items',
                'merchantOrders.merchant:id,name,shop_name',
                'merchantOrders.payment',
                'merchantOrders.orderItems:id,merchant_order_id,product_id,product_variation_id,price,quantity,status_id',
                'merchantOrders.orderItems.product:id,name,slug,total_stock_qty,sku',
                'merchantOrders.orderItems.product.reviews',
                'merchantOrders.orderItems.product.media',
                'merchantOrders.orderItems.product_variant.media',
                'merchantOrders.orderItems.product_variant',
                'merchantOrders.orderItems.product_variant.variations',
                'merchantOrders.orderItems.product_variant.variations.attributeOption',
                'merchantOrders.orderItems.product_variant.variations.attribute',
            ])
            ->first();
    }
    public static function getPaidOrders(Request $request): array
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(7)->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        // ────────────────────────────────────────────────
        // Main query: Only orders where ALL merchant_orders are DELIVERED
        // ────────────────────────────────────────────────
        $query = Order::query()
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereDoesntHave('merchantOrders', function ($q) {
                $q->where('status_id', '!=', OrderStatus::DELIVERED->value);
            })
            ->whereHas('merchantOrders', function ($q) {
                $q->where('status_id', OrderStatus::DELIVERED->value);
            })
            ->with([
                'merchantOrders' => fn($q) => $q->where('status_id', OrderStatus::DELIVERED->value),
                'merchantOrders.items' => fn($q) => $q->where('status_id', OrderStatus::DELIVERED->value)
            ])
            ->select([
                'orders.*',
                DB::raw('COALESCE((
                    SELECT SUM(oi.commission)
                    FROM order_items oi
                    INNER JOIN merchant_orders mo ON oi.merchant_order_id = mo.id
                    WHERE mo.order_id = orders.id
                      AND oi.status_id = ' . OrderStatus::DELIVERED->value . '
                ), 0) as calculated_commission'),
            ]);

        $orders = $query->latest('created_at')->paginate(20)->withQueryString();

        // ────────────────────────────────────────────────
        // Statistics base query (fully delivered orders only)
        // ────────────────────────────────────────────────
        $statsQuery = Order::query()
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereDoesntHave('merchantOrders', fn($q) => $q->where('status_id', '!=', OrderStatus::DELIVERED->value))
            ->whereHas('merchantOrders', fn($q) => $q->where('status_id', OrderStatus::DELIVERED->value));

        $currentStats = $statsQuery->clone()
            ->selectRaw('
                COUNT(DISTINCT orders.id) as total_orders,
                SUM(orders.sub_total) as sub_total,
                SUM(orders.total_shipping_fee) as total_delivery_charges,
                AVG(orders.sub_total) as average_order_value
            ')
            ->first();

        // Total commission from delivered items
        $totalCommission = $statsQuery->clone()
            ->join('merchant_orders', 'orders.id', '=', 'merchant_orders.order_id')
            ->join('order_items', 'merchant_orders.id', '=', 'order_items.merchant_order_id')
            ->where('order_items.status_id', OrderStatus::DELIVERED->value)
            ->sum('order_items.commission');

        // ────────────────────────────────────────────────
        // Previous period (same length period before)
        // ────────────────────────────────────────────────
        $daysDiff = $startDate->diffInDays($endDate) + 1;
        $prevStart = $startDate->copy()->subDays($daysDiff);
        $prevEnd = $startDate->copy()->subDay()->endOfDay();

        $prevStats = Order::query()
            ->whereBetween('orders.created_at', [$prevStart, $prevEnd])
            ->whereDoesntHave('merchantOrders', fn($q) => $q->where('status_id', '!=', OrderStatus::DELIVERED->value))
            ->whereHas('merchantOrders', fn($q) => $q->where('status_id', OrderStatus::DELIVERED->value))
            ->selectRaw('
                COUNT(DISTINCT orders.id) as total_orders,
                SUM(orders.sub_total) as sub_total
            ')
            ->first();

        // ────────────────────────────────────────────────
        // Prepare stats array
        // ────────────────────────────────────────────────
        $stats = [
            'total_orders' => $currentStats->total_orders ?? 0,
            'sub_total' => $currentStats->sub_total ?? 0,
            'total_commission' => $totalCommission ?? 0,
            'total_delivery_charges' => $currentStats->total_delivery_charges ?? 0,
            'average_order_value' => $currentStats->average_order_value ?? 0,

            'order_growth' => self::calcGrowth($prevStats->total_orders ?? 0, $currentStats->total_orders ?? 0),
            'revenue_growth' => self::calcGrowth($prevStats->sub_total ?? 0, $currentStats->sub_total ?? 0),

            // Optional - can be calculated similarly if needed
            'commission_growth' => 0,
            'delivery_growth' => 0,

            'delivered_orders' => $currentStats->total_orders ?? 0,
            'delivered_percentage' => 100,
            'commission_rate' => $currentStats->sub_total > 0
                ? round(($totalCommission / $currentStats->sub_total) * 100, 1)
                : 0,

            // Average delivery charge per order
            'avg_delivery_charge' => $currentStats->total_orders > 0
                ? round(($currentStats->total_delivery_charges ?? 0) / $currentStats->total_orders, 2)
                : 0,
        ];

        return [
            'orders' => $orders,
            'stats' => $stats,
        ];
    }

    private static function calcGrowth($old, $new): float|int
    {
        if ($old == 0) {
            return $new > 0 ? 100 : 0;
        }
        return round((($new - $old) / $old) * 100, 1);
    }
}
