<?php

namespace App\Services\Admin;

use App\Enums\MerchantStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutRequestStatus;
use App\Enums\ShopProductStatus;
use App\Models\Category\Category;
use App\Models\Coupon\Coupon;
use App\Models\Merchant\Merchant;
use App\Models\Merchant\MerchantConfiguration;
use App\Models\Merchant\MerchantOrder;
use App\Models\Order\OrderItem;
use App\Models\Payment\Payout;
use App\Models\Product\Product;
use App\Models\Shop\ShopSetting;
use App\Models\User\User;
use App\Models\Voucher\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    private const DASHBOARD_STATS_CACHE_TTL_SECONDS = 180;
    private const ACCOUNT_STATS_CACHE_TTL_SECONDS = 180;
    private const CHART_DATA_CACHE_TTL_SECONDS = 120;

    public function getDateRange(string $startDate, string $endDate): array
    {
        return [
            'startDate' => Carbon::parse($startDate)->startOfDay(),
            'endDate'   => Carbon::parse($endDate)->endOfDay(),
        ];
    }

    public function getDashboardStatistics(): array
    {
        return Cache::remember('admin:dashboard:statistics', self::DASHBOARD_STATS_CACHE_TTL_SECONDS, function () {
            $orderCounts = MerchantOrder::whereHas('order', function ($query) {
                    $query->notSpam();
                })
                ->whereNotIn('status_id', [0, 9])
                ->select('status_id', DB::raw('count(id) as count'))
                ->groupBy('status_id')
                ->pluck('count', 'status_id')
                ->toArray();

            $shopProductCounts = Product::where('products.status', 1)
                ->where('products.total_stock_qty', '>', 0)
                ->join('shop_products', 'products.id', '=', 'shop_products.product_id')
                ->whereIn('shop_products.status', [
                    ShopProductStatus::APPROVED->value,
                    ShopProductStatus::PENDING->value,
                ])
                ->select('shop_products.status', DB::raw('count(*) as count'))
                ->groupBy('shop_products.status')
                ->pluck('count', 'shop_products.status')
                ->toArray();

            $totalCategory = DB::table(DB::raw('(
                        SELECT id FROM categories
                        UNION ALL
                        SELECT id FROM sub_categories
                        UNION ALL
                        SELECT id FROM sub_category_children
                    ) as category_counts'))->count();

            $topMerchants = Merchant::query()
                ->select(['id', 'shop_name', 'name', 'phone'])
                ->withCount([
                    'orders as total_orders_count',
                    'orders as delivered_orders_count' => function ($query) {
                        $query->where('status_id', OrderStatus::DELIVERED->value);
                    },
                    'orders as cancelled_orders_count' => function ($query) {
                        $query->where('status_id', OrderStatus::CANCELLED->value);
                    }
                ])
                ->withSum(['orders as total_sales_amount' => function ($query) {
                    $query->where('status_id', OrderStatus::DELIVERED->value);
                }], 'grand_total')
                ->addSelect(['total_revenue' => OrderItem::selectRaw('sum(commission)')
                    ->whereHas('merchantOrder', function ($q) {
                        $q->whereColumn('merchant_id', 'merchants.id')
                            ->where('status_id', OrderStatus::DELIVERED->value);
                    })
                    ->where('status_id', OrderStatus::DELIVERED->value)
                ])
                ->orderByDesc('total_sales_amount')
                ->limit(10)
                ->get();

            return [
                'total_products'   => $shopProductCounts[ShopProductStatus::APPROVED->value]  ?? 0,
                'pending_products' => $shopProductCounts[ShopProductStatus::PENDING->value]   ?? 0,
                'active_shops'     => Merchant::where('shop_status', MerchantStatus::Active->value)->count(),
                'total_merchants'  => Merchant::count(),
                'total_customers'  => User::where('role', 3)->count(),
                'merchant_orders'  => array_sum($orderCounts),
                'total_category'   => $totalCategory,
                'total_coupons'    => Coupon::where('end_date', '>=', now())->where('status', 'active')->count(),
                'total_vouchers'   => Voucher::where('end_date', '>=', now())->where('status', 'active')->count(),
                'category'         => Category::with('media')->withCount('products')->orderByDesc('products_count')->limit(5)->get(),
                'order_placed'     => $orderCounts[OrderStatus::PENDING->value]       ?? 0,
                'order_approved'   => $orderCounts[OrderStatus::APPROVED->value]      ?? 0,
                'order_processed'  => $orderCounts[OrderStatus::PROCESSING->value]    ?? 0,
                'order_delivered'  => $orderCounts[OrderStatus::DELIVERED->value]     ?? 0,
                'order_cancelled'  => $orderCounts[OrderStatus::CANCELLED->value]     ?? 0,
                'order_returned'   => $orderCounts[OrderStatus::RETURNED->value]      ?? 0,
                'order_refunded'   => $orderCounts[OrderStatus::REFUNDED->value]      ?? 0,
                'top_merchants'    => $topMerchants,
                'recent_orders'    => MerchantOrder::with(['order:id,invoice_id', 'merchant:id,shop_name'])
                    ->whereHas('order', function ($query) {
                        $query->notSpam();
                    })
                    ->latest()
                    ->select('id', 'order_id', 'created_at', 'status_id', 'merchant_id', 'total_amount', 'grand_total', 'total_items')
                    ->limit(10)
                    ->get(),
            ];
        });
    }

    public function getAccountStatistics(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $cacheKey = sprintf(
            'admin:account-dashboard:statistics:%s:%s',
            $startDate ? $startDate->format('Ymd') : 'all',
            $endDate ? $endDate->format('Ymd') : 'all'
        );

        return Cache::remember($cacheKey, self::ACCOUNT_STATS_CACHE_TTL_SECONDS, function () use ($startDate, $endDate) {
            $approvedPayoutStats = Payout::where('status', PayoutRequestStatus::APPROVED->value)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(amount), 0) as total_sum')
                ->first();

            $pendingPayoutStats = Payout::where('status', '!=', PayoutRequestStatus::APPROVED->value)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(amount), 0) as total_sum')
                ->first();

            $pendingOrderStats = MerchantOrder::query()
                ->whereHas('order', function ($query) {
                    $query->notSpam();
                })
                ->where('status_id', OrderStatus::PENDING->value)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(grand_total), 0) as total_sum')
                ->first();

            $deliveredStats = MerchantOrder::query()
                ->whereHas('order', function ($query) {
                    $query->notSpam();
                })
                ->where('status_id', OrderStatus::DELIVERED->value)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(grand_total), 0) as total_sum')
                ->first();

            $availableBalanceSum = $this->calculateTotalAvailableBalance();

            $totalRevenueSum = OrderItem::whereHas('merchantOrder', function ($query) use ($startDate, $endDate) {
                    $query->where('status_id', OrderStatus::DELIVERED->value);
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    }
                })
                ->where('status_id', OrderStatus::DELIVERED->value)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->sum('commission');

            return [
                'total_paid_count'                   => (int) ($approvedPayoutStats->total_count ?? 0),
                'total_paid_sum'                     => (float) ($approvedPayoutStats->total_sum ?? 0),
                'total_payable_count'                => (int) ($pendingPayoutStats->total_count ?? 0),
                'total_payable_sum'                  => (float) ($pendingPayoutStats->total_sum ?? 0) + $availableBalanceSum,
                'pending_orders_count'               => (int) ($pendingOrderStats->total_count ?? 0),
                'pending_orders_sum'                 => (float) ($pendingOrderStats->total_sum ?? 0),
                'total_delivered_corrected_count'    => (int) ($deliveredStats->total_count ?? 0),
                'total_delivered_corrected_sum'      => (float) ($deliveredStats->total_sum ?? 0),
                'total_revenue_sum'                  => (float) $totalRevenueSum,
            ];
        });
    }

    public function getOrderStatusChartData(): array
    {
        $startDate = request()->filled('start_date')
            ? Carbon::parse(request()->start_date)->startOfDay()
            : now()->subDays(10)->startOfDay();

        $endDate = request()->filled('end_date')
            ? Carbon::parse(request()->end_date)->endOfDay()
            : now()->endOfDay();

        $cacheKey = sprintf(
            'admin:dashboard:chart:%s:%s',
            $startDate->format('Ymd'),
            $endDate->format('Ymd')
        );

        return Cache::remember($cacheKey, self::CHART_DATA_CACHE_TTL_SECONDS, function () use ($startDate, $endDate) {
            $statusGroups = [
                'Pending'    => [OrderStatus::PENDING->value],
                'Processing' => [
                    OrderStatus::APPROVED->value,
                    OrderStatus::PROCESSING->value,
                ],
                'Delivered'  => [OrderStatus::DELIVERED->value],
                'Cancelled'  => [OrderStatus::CANCELLED->value],
            ];

            $dates   = [];
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $dates[] = $current->format('Y-m-d');
                $current->addDay();
            }

            $results = MerchantOrder::query()
                ->selectRaw('DATE(created_at) as order_date, status_id, COUNT(*) as cnt')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('order_date', 'status_id')
                ->get();

            $raw = [];
            foreach ($results as $row) {
                $statusId = $row->status_id instanceof OrderStatus
                    ? $row->status_id->value
                    : (int) $row->status_id;
                $raw[$row->order_date.'.'.$statusId] = (int) $row->cnt;
            }

            $series = [];
            foreach ($statusGroups as $label => $statusIds) {
                $data = [];
                foreach ($dates as $date) {
                    $total = 0;
                    foreach ($statusIds as $statusId) {
                        $key = $date.'.'.$statusId;
                        $total += $raw[$key] ?? 0;
                    }
                    $data[] = $total;
                }

                $series[] = [
                    'name' => $label,
                    'data' => $data,
                ];
            }

            return [
                'categories' => array_map(fn ($d) => Carbon::parse($d)->format('d M'), $dates),
                'series'     => $series,
            ];
        });
    }

    private function calculateTotalAvailableBalance(): float
    {
        $defaultPayoutRequestDays = (int) (ShopSetting::where('key', 'payout_request_date')->value('value') ?? 3);
        $gatewayChargePercent = (float) (ShopSetting::where('key', 'gateway_charge')->value('value') ?? 0);

        $commissionSubQuery = OrderItem::query()
            ->selectRaw('merchant_order_id, COALESCE(SUM(commission), 0) as total_commission')
            ->where('status_id', OrderStatus::DELIVERED->value)
            ->groupBy('merchant_order_id');

        $paidSslPaymentSubQuery = DB::table('order_payments')
            ->select('merchant_order_id')
            ->where('payment_status', PaymentStatus::PAID->value)
            ->where('payment_method', 'SSLCommerz')
            ->groupBy('merchant_order_id');

        $total = MerchantOrder::query()
            ->leftJoinSub($commissionSubQuery, 'order_commissions', function ($join) {
                $join->on('order_commissions.merchant_order_id', '=', 'merchant_orders.id');
            })
            ->leftJoinSub($paidSslPaymentSubQuery, 'paid_ssl', function ($join) {
                $join->on('paid_ssl.merchant_order_id', '=', 'merchant_orders.id');
            })
            ->leftJoin((new MerchantConfiguration())->getTable().' as merchant_configs', 'merchant_configs.merchant_id', '=', 'merchant_orders.merchant_id')
            ->where('merchant_orders.status_id', OrderStatus::DELIVERED->value)
            ->whereNull('merchant_orders.payout_id')
            ->where(function ($query) use ($defaultPayoutRequestDays) {
                $query->where(function ($q) use ($defaultPayoutRequestDays) {
                    $q->whereNotNull('merchant_orders.delivered_at')
                        ->whereRaw(
                            'DATE(merchant_orders.delivered_at) <= DATE_SUB(CURDATE(), INTERVAL COALESCE(merchant_configs.payout_request_date, ?) DAY)',
                            [$defaultPayoutRequestDays]
                        );
                })->orWhere(function ($q) use ($defaultPayoutRequestDays) {
                    $q->whereNull('merchant_orders.delivered_at')
                        ->whereRaw(
                            'DATE(merchant_orders.updated_at) <= DATE_SUB(CURDATE(), INTERVAL COALESCE(merchant_configs.payout_request_date, ?) DAY)',
                            [$defaultPayoutRequestDays]
                        );
                });
            })
            ->selectRaw(
                'COALESCE(SUM(
                    (merchant_orders.sub_total - CASE WHEN merchant_orders.bear_by_packly != 1 THEN merchant_orders.discount_amount ELSE 0 END)
                    - COALESCE(order_commissions.total_commission, 0)
                    - CASE WHEN paid_ssl.merchant_order_id IS NOT NULL THEN (merchant_orders.sub_total * ? / 100) ELSE 0 END
                ), 0) as available_balance_sum',
                [$gatewayChargePercent]
            )
            ->value('available_balance_sum');

        return (float) $total;
    }
}
