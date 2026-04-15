<?php

namespace Modules\Api\V1\Merchant\Dashboard\Http\Controllers;

use App\Enums\AccountTypes;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Merchant\MerchantOrder;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Product\Product;
use App\Models\Purchase\Purchase;
use App\Models\Sell\SellProduct;
use App\Services\ApiResponse;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-dashboard-counts')->only('index');
        $this->middleware('shop.permission:show-dashboard-order-summary')->only('orderSummary');
        $this->middleware('shop.permission:show-dashboard-sales-purchase-expanse')->only('purchasesSalesExpansesSummary');
        $this->middleware('shop.permission:show-dashboard-top-selling-products')->only('topSellingProducts');
        $this->middleware('shop.permission:show-dashboard-orders-with-items')->only('ordersWithItems');
        $this->middleware('shop.permission:show-dashboard-sidebar-counts')->only('sidebarCounts');
    }
    /*
     * Loads dashboard overview for the authenticated user.
     */
    public function index()
    {
        $merchantId = Auth::user()->merchant->id;

        $accountSums = DB::table('accounts')
            ->selectRaw("
            SUM(CASE WHEN account_type = ? AND uucode = 'SALE' THEN balance ELSE 0 END) as sales,
            SUM(CASE WHEN account_type = ? AND uucode = 'INPU' THEN balance ELSE 0 END) as purchase,
            SUM(CASE WHEN account_type = ? AND uucode = 'ACPA' THEN balance ELSE 0 END) as account_payable,
            SUM(CASE WHEN account_type = ? AND uucode = 'NETP' THEN balance ELSE 0 END) as net_profit,
            SUM(CASE WHEN account_type = ? THEN balance ELSE 0 END) as expense,
            SUM(CASE WHEN account_type = ? AND uucode = 'SPRC' THEN balance ELSE 0 END) as sale_due,
            SUM(CASE WHEN account_type = ? AND uucode = 'REVE' THEN balance ELSE 0 END) as revenue,
            SUM(CASE WHEN account_type = ? AND uucode = 'NOTP' THEN balance ELSE 0 END) as liabilities,
            SUM(CASE WHEN account_type = ? AND uucode = 'PYRC' THEN balance ELSE 0 END) as payable_receivable_pending,
            SUM(CASE WHEN account_type = ? AND uucode = 'COMM' THEN balance ELSE 0 END) as commission
        ", [
                AccountTypes::SALE->value,
                AccountTypes::PURCHASE->value,
                AccountTypes::LIABILITIES->value,
                AccountTypes::SALE->value,
                AccountTypes::EXPENSE->value,
                AccountTypes::ASSET->value,
                AccountTypes::INCOME->value,
                AccountTypes::LIABILITIES->value,
                AccountTypes::ASSET->value,
                AccountTypes::EXPENSE->value,
            ])
            ->where('merchant_id', $merchantId)
            ->first();

        $payableReceivableReceived = DB::table('payouts')
            ->where('merchant_id', $merchantId)
            ->where('status', 3)
            ->sum('amount');

        $productCount = Product::where('merchant_id', $merchantId)->count();
        // $customerCount = Customer::where('merchant_id', $merchantId)->count();

        return ApiResponse::success('success', [
            'total_products'  => $productCount,
            'total_purchases' => (float) $accountSums->purchase,
            'total_expense'   => (float) $accountSums->expense,
            'total_sales'     => (float) $accountSums->sales,
            'net_profit'      => (float) $accountSums->net_profit,
            // 'gross_profit' => (float) $accountSums->net_profit, // TODO: Calculate gross profit
            'total_due' => (float) $accountSums->account_payable,
            // 'total_customers' => $customerCount, // TODO: Calculate total customers
            'total_sale_due' => (float) $accountSums->sale_due,
            // 'total_revenue' => (float) $accountSums->revenue, // TODO: Calculate total revenue
            // 'total_liabilities' => (float) $accountSums->liabilities, // TODO: Calculate total liabilities
            'earning_from_packly' => (float) $payableReceivableReceived,
            // TODO: Calculate commission
            // 'earning_from_packly' => [
            //     'total' => (float) $payableReceivableReceived + (float) $accountSums->payable_receivable_pending,
            //     'received' => (float) $payableReceivableReceived,
            //     'pending' => (float) $accountSums->payable_receivable_pending,
            //     'commission' => (float) $accountSums->commission,
            // ],
        ], Response::HTTP_OK);
    }

    /*
     * Returns data for sidebar KPIs.
     */
    public function sidebarCounts(): JsonResponse
    {
        $merchantId = Auth::user()->merchant->id;

        return ApiResponse::success('Sidebar counts', [
            'pending_orders' => (int) MerchantOrder::where([
                'merchant_id' => $merchantId,
                'status_id'   => OrderStatus::PENDING->value,
                'is_seen'     => 0,
            ])->count(),
        ], Response::HTTP_OK);
    }

    /*
     * Displays top-selling products for dashboard insight.
     */
    public function topSellingProducts(Request $request): JsonResponse
    {
        $merchantId = Auth::user()->merchant->id;
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        $topProducts = Product::join('sell_product_details', 'products.id', '=', 'sell_product_details.product_id')
            ->join('sell_products', 'sell_product_details.sell_product_id', '=', 'sell_products.id')
            ->where('sell_products.merchant_id', $merchantId)
            ->when($startDate, fn($q) => $q->whereDate('sell_products.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('sell_products.created_at', '<=', $endDate))
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sell_product_details.sale_qty) as total_sales')
            )->with(['attributes:product_id,attribute_option_id,attribute_id', 'attributes.attributeOption:id,attribute_value', 'attributes.attribute:id,name'])
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        return ApiResponse::success('Top selling products', $topProducts, Response::HTTP_OK);
    }

    public function topSellingProductDetails(Request $request,$productId): JsonResponse
    {
        $merchantId = Auth::user()->merchant->id;

        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        $product = Product::with(['attributes:product_id,attribute_option_id,attribute_id', 'attributes.attributeOption:id,attribute_value', 'attributes.attribute:id,name'])
            ->where('merchant_id', $merchantId)
            ->find($productId);

        if (!$product) {
            return ApiResponse::failure('Product not found or does not belong to your merchant account', Response::HTTP_NOT_FOUND);
        }

        $saleData = DB::table('sell_product_details')
            ->join('sell_products', 'sell_product_details.sell_product_id', '=', 'sell_products.id')
            ->where('sell_products.merchant_id', $merchantId)
            ->where('sell_product_details.product_id', $productId)
            ->when($startDate, fn($q) => $q->whereDate('sell_products.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('sell_products.created_at', '<=', $endDate))
            ->select(
                DB::raw('SUM(sell_product_details.sale_qty) as total_sold'),
                DB::raw('SUM(sell_product_details.sub_total) as revenue'),
                DB::raw('MIN(sell_products.created_at) as first_sale_date')
            )
            ->first();

        $totalSold = (int) ($saleData->total_sold ?? 0);
        $revenue   = (float) ($saleData->revenue ?? 0);
        
        $avgDailySales = 0;
        if ($totalSold > 0 && $saleData->first_sale_date) {
            $firstSale = Carbon::parse($saleData->first_sale_date);
            $now = Carbon::now();
            $daysSinceFirstSale = max(1, $firstSale->diffInDays($now)); // At least 1 day
            $avgDailySales = round($totalSold / $daysSinceFirstSale);
        }
        $recentOrders = MerchantOrder::with(['items' => function ($query) use ($productId) {
            $query->where('product_id', $productId);
        }, 'order', 'payment'])
        ->whereHas('items', function ($query) use ($productId) {
            $query->where('product_id', $productId);
        })
        ->where('merchant_id', $merchantId)
        ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
        ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
        ->orderByDesc('created_at')
        ->limit(5)
        ->get()
        ->map(function ($order) {
            return [
                'order_invoice'     => $order->order ? $order->order->invoice_id : 'N/A',
                'status'         => $order->status_label,
                'status_color'   => $order->status_bg_color,
                'date'           => $order->created_at->format('M d, Y h:ia'),
                'price'          => $order->grand_total,
                'items'          => $order->items->count(),
                'payment_method' => $order->payment->payment_method ?? 'N/A'
            ];
        });

        $responseData = [
            'product' => [
                'id'        => $product->id,
                'name'      => $product->name,
                'sku'       => $product->sku,
                'thumbnail' => $product->thumbnail,
            ],
            'kpis' => [
                'total_sold'      => $totalSold,
                'revenue'         => $revenue,
                'in_stock'        => $product->total_stock_qty,
                'avg_daily_sales' => $avgDailySales,
            ],
            'recent_orders' => $recentOrders
        ];

        return ApiResponse::success('Top selling product details', $responseData, Response::HTTP_OK);
    }

    /*
     * Returns summary of orders for the dashboard.
     */
    public function orderSummary(Request $request): JsonResponse
    {
        $merchantId = Auth::user()->merchant->id;

        $fromDate = $request->query('start_date');
        $toDate   = $request->query('end_date');

        $query = MerchantOrder::where('merchant_id', $merchantId)
            ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('created_at', [$fromDate, $toDate]);
            });

        $orders = (clone $query)
            ->selectRaw('status_id, COUNT(*) as count')
            ->groupBy('status_id')
            ->get()
            ->mapWithKeys(function ($order) {
                return [$order->status_id->value => $order->count];
            });

        $totalOrders = $query->count();

        $statuses = [
            'returned'   => OrderStatus::RETURNED->value,
            'delivered'  => OrderStatus::DELIVERED->value,
            'cancelled'  => OrderStatus::CANCELLED->value,
            'pending'    => OrderStatus::PENDING->value,
            'processing' => OrderStatus::PROCESSING->value,
        ];

        $totalReturnedOrders   = $orders[$statuses['returned']]      ?? 0;
        $totalDeliveredOrders  = $orders[$statuses['delivered']]     ?? 0;
        $totalCancelledOrders  = $orders[$statuses['cancelled']]     ?? 0;
        $totalPendingOrders    = $orders[$statuses['pending']]       ?? 0;
        $totalProcessingOrders = $orders[$statuses['processing']]    ?? 0;

        $percentage = fn($count) => $totalOrders > 0 ? number_format(($count / $totalOrders) * 100, 2) : 0;

        return ApiResponse::success('success', [
            'total_orders'                       => $totalOrders,
            'total_returned_orders'              => $totalReturnedOrders,
            'total_delivered_orders'             => $totalDeliveredOrders,
            'total_cancelled_orders'             => $totalCancelledOrders,
            'total_pending_orders'               => $totalPendingOrders,
            'total_processing_orders'            => $totalProcessingOrders,
            'total_returned_orders_percentage'   => $percentage($totalReturnedOrders),
            'total_delivered_orders_percentage'  => $percentage($totalDeliveredOrders),
            'total_cancelled_orders_percentage'  => $percentage($totalCancelledOrders),
            'total_pending_orders_percentage'    => $percentage($totalPendingOrders),
            'total_processing_orders_percentage' => $percentage($totalProcessingOrders),
        ], Response::HTTP_OK);
    }

    /*
     * Shows purchase, sales, and expense summary.
     */
    public function purchasesSalesExpansesSummary(Request $request): JsonResponse
    {
        return ApiResponse::success('success', [
            'sales_purchases_expanses_data' => $this->getSalesPurchaseExpanseData($request),
        ], Response::HTTP_OK);
    }

    private function getSalesPurchaseExpanseData($request): array
    {
        $request->validate([
            'period' => 'in:year,month',
        ]);

        $merchantId = Auth::user()->merchant->id;

        $now = Carbon::now();

        if ($request->period === 'month') {
            $startDate  = $now->startOfMonth();
            $endDate    = $now->copy()->endOfMonth();
            $dateFormat = '%Y-%m-%d';

            $dates = collect(CarbonPeriod::create($startDate, '1 day', $endDate))->map(fn($date) => $date->format('Y-m-d'))->toArray();
        } else {
            $startDate  = $now->startOfYear();
            $endDate    = $now->copy()->endOfYear();
            $dateFormat = '%Y-%m';

            $dates = collect(CarbonPeriod::create($startDate, '1 month', $endDate))->map(fn($date) => $date->format('Y-m'))->toArray();
        }

        $purchases = Purchase::where('merchant_id', $merchantId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->selectRaw(
                "
                DATE_FORMAT(purchase_date, '{$dateFormat}') as date,
                SUM(grand_total) as total_amount,
                COUNT(*) as transaction_count
            ",
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $sales = SellProduct::where('merchant_id', $merchantId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->whereNot('sold_from', 'Ecommerce')
            ->selectRaw(
                "
                DATE_FORMAT(sale_date, '{$dateFormat}') as date,
                SUM(grand_total) as total_amount,
                COUNT(*) as transaction_count
            ",
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $expanses = MerchantTransaction::whereHas('account', function ($query) use ($merchantId) {
            $query->where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::EXPENSE->value]);
        })
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw(
                "
                DATE_FORMAT(date, '{$dateFormat}') as expense_date,
                SUM(amount) as total_amount,
                COUNT(*) as transaction_count
            ",
            )
            ->groupBy('expense_date')
            ->orderBy('expense_date')
            ->get();

        return [
            'dates'  => $dates,
            'series' => [
                [
                    'name' => 'Purchases',
                    'data' => $this->formatSeriesData($purchases, $dates),
                ],
                [
                    'name' => 'Sales',
                    'data' => $this->formatSeriesData($sales, $dates),
                ],
                [
                    'name' => 'Expenses',
                    'data' => $this->formatSeriesData($expanses, $dates),
                ],
            ],
            'summary' => [
                'total_sales'           => $sales->sum('total_amount'),
                'total_purchases'       => $purchases->sum('total_amount'),
                'total_sales_count'     => $sales->sum('transaction_count'),
                'total_purchases_count' => $purchases->sum('transaction_count'),
                'total_expanses'        => $expanses->sum('total_amount'),
                'period'                => $request->period,
            ],
        ];
    }

    /*
     * Lists orders with their product items.
     */
    public function ordersWithItems(Request $request): JsonResponse
    {
        $merchantId = Auth::user()->merchant->id;

        $orders = MerchantOrder::with([
            'items' => function ($query) {
                $query->with(['product' => function ($q) {
                    $q->withTrashed(); // include soft-deleted products
                }, 'product_variant']);
            },
            'order',
            'payment:merchant_order_id,payment_method',
        ])
            ->where('merchant_id', $merchantId)
            ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        $formattedOrders = $orders->getCollection()->map(function ($order) {
            $orderArray               = $order->toArray();
            $orderArray['created_at'] = $order->created_at->format('Y-m-d h:i A');

            return $orderArray;
        });

        $data = $orders->setCollection($formattedOrders);

        return ApiResponse::success('Orders with items', $data, Response::HTTP_OK);
    }

    private function formatSeriesData($data, $dates): array
    {
        $formattedData = [];
        $dataByDate    = $data->pluck('total_amount', 'date')->toArray();

        foreach ($dates as $date) {
            $formattedData[] = $dataByDate[$date] ?? 0;
        }

        return $formattedData;
    }
}
