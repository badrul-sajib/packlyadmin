<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Merchant\Merchant;
use App\Models\Merchant\MerchantConfiguration;
use App\Models\Merchant\MerchantOrder;
use App\Models\Order\OrderItem;
use App\Models\Payment\Payout;
use App\Models\Shop\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PayableController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:payable-list')->only('index');
        $this->middleware('permission:payable-show')->only('orders');
    }

    public function index()
    {
        $filter = request('filter', 'all');
        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $offset = ($page - 1) * $perPage;

        $pendingPayoutsQuery = Payout::query()
            ->where('status', '!=', PayoutRequestStatus::APPROVED->value)
            ->with('merchant')
            ->withCount(['merchantOrders', 'payoutMerchantOrders']);

        $pendingPayoutCount = (clone $pendingPayoutsQuery)->count();
        $pendingPayoutSum = (float) (clone $pendingPayoutsQuery)->toBase()->sum('amount');

        $defaultPayoutRequestDays = (int) (ShopSetting::where('key', 'payout_request_date')->value('value') ?? 3);
        $gatewayChargePercent = (float) (ShopSetting::where('key', 'gateway_charge')->value('value') ?? 0);

        $commissionSubQuery = OrderItem::query()
            ->selectRaw('merchant_order_id, COALESCE(SUM(commission), 0) as total_commission')
            ->where('status_id', OrderStatus::DELIVERED->value)
            ->groupBy('merchant_order_id');

        $paidSslPaymentSubQuery = DB::connection('mysql_internal')->table('order_payments')
            ->select('merchant_order_id')
            ->where('payment_status', PaymentStatus::PAID->value)
            ->where('payment_method', 'SSLCommerz')
            ->groupBy('merchant_order_id');

        $availableBaseQuery = DB::connection('mysql_internal')->table('merchant_orders')
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
                'merchant_orders.merchant_id,
                 COUNT(merchant_orders.id) as orders_count,
                 COALESCE(SUM(
                    (merchant_orders.sub_total - CASE WHEN merchant_orders.bear_by_packly != 1 THEN merchant_orders.discount_amount ELSE 0 END)
                    - COALESCE(order_commissions.total_commission, 0)
                    - CASE WHEN paid_ssl.merchant_order_id IS NOT NULL THEN (merchant_orders.sub_total * ? / 100) ELSE 0 END
                 ), 0) as available_balance_sum',
                [$gatewayChargePercent]
            )
            ->groupBy('merchant_orders.merchant_id')
            ->having('available_balance_sum', '>', 0);

        $availableCount = DB::query()->fromSub($availableBaseQuery, 'avail')->count();
        $availableSum = (float) DB::query()->fromSub($availableBaseQuery, 'avail')->sum('available_balance_sum');

        $showPayouts = in_array($filter, ['all', 'payout'], true);
        $showAvailable = in_array($filter, ['all', 'available'], true);

        $totalCount = ($showPayouts ? $pendingPayoutCount : 0) + ($showAvailable ? $availableCount : 0);
        $totalSum = ($showPayouts ? $pendingPayoutSum : 0) + ($showAvailable ? $availableSum : 0);

        $items = collect();
        $remaining = $perPage;

        if ($showPayouts && $offset < $pendingPayoutCount) {
            $take = min($remaining, $pendingPayoutCount - $offset);
            $payouts = (clone $pendingPayoutsQuery)
                ->orderBy('created_at', 'desc')
                ->skip($offset)
                ->take($take)
                ->get()
                ->map(function ($payout) {
                    $ordersCount = $payout->merchant_orders_count ?: $payout->payout_merchant_orders_count;
                    return (object) [
                        'merchant_info' => (object) [
                            'shop_name' => $payout->merchant->shop_name ?? 'N/A',
                            'merchant_name' => $payout->merchant->name ?? 'N/A',
                            'contact_number' => $payout->merchant->phone ?? 'N/A',
                        ],
                        'amount' => (float) $payout->amount,
                        'type' => 'Payout Request',
                        'status' => $payout->status_label['value'],
                        'orders_count' => $ordersCount,
                        'id' => $payout->id,
                        'is_payout' => true,
                    ];
                });

            $items = $items->concat($payouts);
            $remaining -= $take;
            $offset = 0;
        } else {
            $offset -= $pendingPayoutCount;
        }

        if ($showAvailable && $remaining > 0) {
            $availableRows = DB::query()
                ->fromSub($availableBaseQuery, 'avail')
                ->join('merchants', 'merchants.id', '=', 'avail.merchant_id')
                ->select([
                    'avail.merchant_id',
                    'avail.orders_count',
                    'avail.available_balance_sum',
                    'merchants.shop_name',
                    'merchants.name as merchant_name',
                    'merchants.phone as contact_number',
                ])
                ->orderByDesc('avail.available_balance_sum')
                ->offset($offset)
                ->limit($remaining)
                ->get()
                ->map(function ($row) {
                    return (object) [
                        'merchant_info' => (object) [
                            'shop_name' => $row->shop_name ?? 'N/A',
                            'merchant_name' => $row->merchant_name ?? 'N/A',
                            'contact_number' => $row->contact_number ?? 'N/A',
                        ],
                        'amount' => (float) $row->available_balance_sum,
                        'type' => 'Available Balance',
                        'status' => 'Available',
                        'orders_count' => (int) $row->orders_count,
                        'id' => $row->merchant_id,
                        'is_payout' => false,
                    ];
                });

            $items = $items->concat($availableRows);
        }

        $allPayables = new LengthAwarePaginator(
            $items,
            $totalCount,
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => request()->query()]
        );

        return view('Admin::payables.index', compact('allPayables', 'totalCount', 'totalSum', 'filter'));
    }

    public function orders(Request $request)
    {
        $id = $request->id;
        $isPayout = filter_var($request->is_payout, FILTER_VALIDATE_BOOLEAN);

        if ($isPayout) {
            $payout = Payout::findOrFail($id);
            $orders = $payout->merchantOrders()->count() > 0 
                ? $payout->merchantOrders()->with(['order', 'merchant'])->paginate(15)
                : $payout->payoutMerchantOrders()->with(['order', 'merchant'])->paginate(15);
            $title = "Orders for Payout #{$payout->id}";
        } else {
            $merchant = Merchant::findOrFail($id);
            $orders = $merchant->orders()
                ->where('status_id', OrderStatus::DELIVERED->value)
                ->whereNull('payout_id')
                ->with(['order', 'merchant'])
                ->paginate(15);
            $title = "Pending Payout Orders for {$merchant->shop_name}";
        }

        return view('Admin::payables.orders', compact('orders', 'title'));
    }
}
