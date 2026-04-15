<?php

namespace App\Http\Controllers\Admin\Order;

use App\Actions\FetchMerchantOrders;
use App\Enums\AccountTypes;
use App\Enums\CancelBy;
use App\Enums\CourierStatus;
use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\OrderStatusTimelineTypes;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantOrder;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Order\Order;
use App\Models\Setting\ShopSetting;
use App\Models\Stock\StockOrder;
use App\Services\ApiResponse;
use App\Services\InsideDhakaService;
use App\Services\Order\MerchantOrderService;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        protected MerchantOrderService $merchantOrderService
    ) {
        $this->middleware('permission:order-list')->only(['index', 'merchantOrders']);
        $this->middleware('permission:order-show')->only('show');
        $this->middleware('permission:order-update')->only(['merchantOrderEdit', 'merchantOrderUpdate', 'merchantOrderStatusChange', 'merchantOrderAddNote']);
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            
            $request->merge([
                'start_date' => now()->toDateString(),
                'end_date' => now()->toDateString(),
            ]);
        }

        $orders = OrderService::getOrders($request);
        if ($request->ajax()) {
            return view('components.orders.table', ['entity' => $orders])->render();
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return view('Admin::orders.index', compact('orders', 'startDate', 'endDate'));
    }

    public function show($invoiceId)
    {
        $order = OrderService::getOrderByInvoiceId($invoiceId);
        $host = ShopSetting::where('key', 'app_e_commerce_url')->first()->value ?? '';

        return view('Admin::orders.show', compact('order', 'host'));
    }

    /**
     * @throws Throwable
     */
    public function merchantOrders(Request $request, FetchMerchantOrders $fetchMerchantOrders)
    {
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $request->merge([
                'start_date' => now()->toDateString(),
                'end_date' => now()->toDateString(),
            ]);
        }

        $orders = $fetchMerchantOrders->execute($request);

        if ($request->ajax()) {
            return view('components.orders.merchant_table', ['entity' => $orders])->render();
        }
        $startDate = $request->input('start_date') ?: now()->toDateString();
        $endDate = $request->input('end_date') ?: now()->toDateString();

        return view('Admin::orders.merchant_orders', compact('orders', 'startDate', 'endDate'));
    }

    public function merchantOrderShow($invoice)
    {
        $merchantOrder = $this->resolveMerchantOrder($invoice);

        return view('Admin::orders.merchant_order_show', compact('merchantOrder'));
    }

    public function merchantOrdersNoActivity(Request $request)
    {
        $thresholdDays = (int) (ShopSetting::where('key', 'order_no_activity_days')->value('value') ?? 3);
        if ($thresholdDays < 1) {
            $thresholdDays = 1;
        }

        $cutoff = now()->subDays($thresholdDays);
        $perPage = (int) $request->input('perPage', 10);
        $search = trim((string) $request->input('search', ''));
        $inactiveStatuses = [
            OrderStatus::PENDING->value,
            OrderStatus::APPROVED->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::READY_TO_SHIP->value,
        ];

        $orders = MerchantOrder::query()
            ->with(['merchant', 'order'])
            ->withCount(['orderItems as order_items_count'])
            ->whereHas('order', function ($query) {
                $query->notSpam();
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('tracking_id', 'like', "%{$search}%")
                        ->orWhere('invoice_id', 'like', "%{$search}%")
                        ->orWhere('consignment_id', 'like', "%{$search}%")
                        ->orWhereHas('merchant', function ($mq) use ($search) {
                            $mq->whereAny(['name', 'phone', 'shop_name'], 'like', "%{$search}%");
                        })
                        ->orWhereHas('order', function ($oq) use ($search) {
                            $oq->where('invoice_id', 'like', "%{$search}%")
                                ->orWhere('customer_name', 'like', "%{$search}%")
                                ->orWhere('customer_number', 'like', "%{$search}%");
                        });
                });
            })
            ->whereIn('status_id', $inactiveStatuses)
            ->where('updated_at', '<=', $cutoff)
            ->orderBy('updated_at', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return view('components.orders.merchant_table', ['entity' => $orders])->render();
        }

        return view('Admin::orders.no_activity', [
            'orders' => $orders,
            'thresholdDays' => $thresholdDays,
            'cutoff' => $cutoff,
        ]);
    }

    public function ordersMonitoring(Request $request)
    {
        $perPage = (int) $request->input('perPage', 10);
        $search  = trim((string) $request->input('search', ''));

        $orders = MerchantOrder::query()
            ->with(['merchant', 'order', 'payment'])
            ->whereHas('order', fn($q) => $q->notSpam())
            ->whereNotNull('mismatch_detected_at')->whereIn('courier_status', ['delivered', 'partial_delivered', 'cancelled'])->orderBy('mismatch_detected_at', 'desc')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('tracking_id', 'like', "%{$search}%")
                        ->orWhere('invoice_id', 'like', "%{$search}%")
                        ->orWhere('consignment_id', 'like', "%{$search}%")
                        ->orWhereHas('merchant', fn($mq) => $mq->whereAny(['name', 'phone', 'shop_name'], 'like', "%{$search}%"))
                        ->orWhereHas('order', fn($oq) => $oq->where('invoice_id', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return view('components.orders.monitoring_table', compact('orders'))->render();
        }

        return view('Admin::orders.monitoring', compact('orders'));
    }

    public function merchantOrderCancel(Request $request, MerchantOrder $merchantOrder): RedirectResponse
    {
        $request->validate([
            'cancel_note' => 'required|string|max:500',
        ]);

        if ($this->isPayoutLocked($merchantOrder)) {
            return $this->redirectForPayoutLock($merchantOrder);
        }

        if (! $this->canEditMerchantOrder($merchantOrder)) {
            return to_route('admin.orders.merchant.show', $merchantOrder->invoice_id ?? $merchantOrder->order->invoice_id)
                ->with('error', 'This merchant order can no longer be cancelled.');
        }

        if ($merchantOrder->status_id?->value === OrderStatus::CANCELLED->value) {
            return to_route('admin.orders.merchant.show', $merchantOrder->invoice_id ?? $merchantOrder->order->invoice_id)
                ->with('error', 'This merchant order is already cancelled.');
        }

        try {
            DB::beginTransaction();

            $merchantOrder->orderItems()->update([
                'status_id' => OrderStatus::CANCELLED->value,
            ]);

            $merchantOrder->update([
                'status_id' => OrderStatus::CANCELLED->value,
                'cancel_by' => CancelBy::ADMIN->value,
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
                'cancel_note' => $request->input('cancel_note'),
            ]);

            if ($merchantOrder->payment && $merchantOrder->payment->payment_status == PaymentStatus::PENDING->value) {
                $merchantOrder->payment->update(['payment_status' => PaymentStatus::CANCELLED->value]);
            }

            $this->merchantOrderService->recalculateTotals($merchantOrder->fresh());

            $order = $merchantOrder->order;
            if ($order && $order->merchantOrders()->where('status_id', '!=', OrderStatus::CANCELLED->value)->count() === 0) {
                $order->update(['status_id' => OrderStatus::CANCELLED->value]);
            }

            $admin = Auth::user();
            $merchantOrder->orderTimeLines()->create([
                'status_id' => OrderStatus::CANCELLED->value,
                'date' => now(),
                'type' => OrderStatusTimelineTypes::ORDER->value,
                'message' => 'Cancelled by admin '.($admin->name ?? 'Admin').($admin?->phone ? ' ('.$admin->phone.')' : ''),
                'note' => $request->input('cancel_note'),
            ]);

            DB::commit();

            return to_route('admin.orders.merchant.show', $merchantOrder->invoice_id ?? $merchantOrder->order->invoice_id)
                ->with('success', 'Merchant order cancelled successfully.');
        } catch (Throwable $th) {
            DB::rollBack();

            return to_route('admin.orders.merchant.show', $merchantOrder->invoice_id ?? $merchantOrder->order->invoice_id)
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function merchantOrderEdit($invoice)
    {
        $merchantOrder = $this->resolveMerchantOrder($invoice);

        if ($this->isPayoutLocked($merchantOrder)) {
            return $this->redirectForPayoutLock($merchantOrder);
        }

        if (! $this->canEditMerchantOrder($merchantOrder)) {
            return to_route('admin.orders.merchant.show', $merchantOrder->invoice_id ?? $merchantOrder->order->invoice_id)
                ->with('error', 'This merchant order can no longer be edited.');
        }

        $activities = Activity::query()
            ->where('subject_type', MerchantOrder::class)
            ->where('subject_id', $merchantOrder->id)
            ->with('causer')
            ->latest()
            ->get();

        return view('Admin::orders.edit', compact('merchantOrder', 'activities'));
    }

    public function merchantOrderAddNote(Request $request, MerchantOrder $merchantOrder): JsonResponse
    {
        if ($this->isPayoutLocked($merchantOrder)) {
            return $this->jsonPayoutLockResponse();
        }

        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $admin = Auth::user();

        $log = activity()
            ->performedOn($merchantOrder)
            ->useLog('admin_note')
            ->causedBy($admin)
            ->withProperties([
                'note' => $request->input('note'),
                'admin_name' => $admin->name ?? 'Admin',
                'admin_phone' => $admin->phone ?? null,
                'added_at' => now(),
            ])
            ->log('Admin added a note to merchant order');

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully.',
            'data' => [
                'note' => $request->input('note'),
                'admin_name' => $admin->name ?? 'Admin',
                'admin_phone' => $admin->phone ?? null,
                'created_at' => $log->created_at?->format('d/m/Y h:i A'),
            ],
        ]);
    }

    public function updateOrderShipping(Order $order, Request $request): JsonResponse
    {
        if ($order->merchantOrders()->whereNotNull('payout_id')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'One or more merchant orders are locked because payout has already been created.',
            ], 403);
        }

        $request->validate([
            'shipping_type' => 'required|string|in:ISD,OSD',
            'shipping_amount' => 'required|numeric|min:0',
            'customer_address' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $newTotalShippingFee = $request->shipping_amount;

            // Update Main Order
            $order->update([
                'customer_address' => $request->customer_address,
                'shipping_type' => $request->shipping_type,
                'total_shipping_fee' => $newTotalShippingFee,
                'grand_total' => $order->sub_total + $newTotalShippingFee - $order->total_discount,
            ]);

            // If multiple merchant orders, distribute the shipping fee
            $merchantOrders = $order->merchantOrders;
            foreach ($merchantOrders as $index => $mOrder) {
                $shippingAmount = ($index === 0) ? $newTotalShippingFee : 0;
                $mOrder->update([
                    'shipping_amount' => $shippingAmount,
                    'grand_total' => $mOrder->sub_total + $shippingAmount - $mOrder->discount_amount,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order shipping details updated successfully.',
                'data' => [
                    'total_shipping_fee' => number_format($newTotalShippingFee, 2),
                    'grand_total' => number_format($order->grand_total, 2),
                    'customer_address' => $order->customer_address,
                    'shipping_type' => $order->shipping_type,
                ]
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order shipping details: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function merchantOrderStatusChange(Request $request, MerchantOrder $merchantOrder): JsonResponse
    {
        if ($this->isPayoutLocked($merchantOrder)) {
            return $this->jsonPayoutLockResponse();
        }

        $request->validate([
            'status_id' => 'required|integer',
        ]);

        $newStatus = (int) $request->input('status_id');

        $currentStatus = $merchantOrder->status_id->value;

        if ($currentStatus === $newStatus) {
            return response()->json([
                'success' => false,
                'message' => 'Order already in this status.',
            ], 422);
        }

        if (!$this->validateAdminStatus($currentStatus, $newStatus)) {
            return response()->json([
                'success' => false,
                'message' => OrderStatus::getStatusLabel($currentStatus) . ' cannot move to ' . OrderStatus::getStatusLabel($newStatus),
            ], 422);
        }

        if ($newStatus === OrderStatus::READY_TO_SHIP->value) {
            $insufficientStockItems = $this->getInsufficientStockItems($merchantOrder);
            if (! empty($insufficientStockItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ready to Ship failed. Stock is not available for: '.implode(', ', $insufficientStockItems),
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $merchantOrder = MerchantOrder::lockForUpdate()->findOrFail($merchantOrder->id);
            $order = $merchantOrder->order;

            $merchantOrder->status_id = $newStatus;

            $merchantOrder->save();

            $merchantOrder->items()
                ->whereNotIn('status_id', [
                    OrderStatus::CANCELLED->value,
                    OrderStatus::RETURNED->value,
                    OrderStatus::REFUNDED->value
                ])
                ->update([
                    'status_id' => $newStatus,
                    'action_by' => Auth::id()
                ]);

            // Special handling for Delivered <-> Cancelled toggles
            if ($currentStatus == OrderStatus::DELIVERED->value && $newStatus == OrderStatus::CANCELLED->value) {
                // Move delivered items back to cancelled
                $merchantOrder->items()
                    ->where('status_id', OrderStatus::DELIVERED->value)
                    ->update(['status_id' => OrderStatus::CANCELLED->value, 'action_by' => Auth::id()]);
                // Reset delivery markers and payment
                $merchantOrder->delivered_at = null;
                $merchantOrder->save();
                if ($merchantOrder->payment) {
                    $merchantOrder->payment->payment_status = PaymentStatus::CANCELLED->value;
                    $merchantOrder->payment->save();
                }
            }

            if ($currentStatus == OrderStatus::CANCELLED->value && $newStatus == OrderStatus::DELIVERED->value) {
                // Move cancelled items to delivered
                $merchantOrder->items()
                    ->where('status_id', OrderStatus::CANCELLED->value)
                    ->update(['status_id' => OrderStatus::DELIVERED->value, 'action_by' => Auth::id()]);
                $merchantOrder->delivered_at = now();
                $merchantOrder->save();
                if ($merchantOrder->payment && $merchantOrder->payment->payment_method == 'COD') {
                    $merchantOrder->payment->payment_status = PaymentStatus::PAID->value;
                    $merchantOrder->payment->save();
                }
                // Create ledger for delivered
                $this->ledgerCreate($merchantOrder);
            }

            $timeline = $merchantOrder->orderTimeLines()
                ->where('status_id', $newStatus)
                ->first();

            if ($timeline) {
                $timeline->update(['date' => now()]);
            } else {
                $admin = Auth::user();
                $merchantOrder->orderTimeLines()->create([
                    'status_id' => $newStatus,
                    'date' => now(),
                    'type' => OrderStatusTimelineTypes::ORDER->value,
                    'message' => 'Status changed by admin ' . ($admin->name ?? 'Admin') . ($admin?->phone ? ' (' . $admin->phone . ')' : ''),
                ]);
            }

            if ($newStatus == OrderStatus::READY_TO_SHIP->value) {
                $sfcConfig = ShopSetting::whereIn('key', ['sfc_base_url', 'sfc_public_key', 'sfc_secret_key'])->pluck('value', 'key')->toArray();
                if (!empty($sfcConfig['sfc_base_url']) && !empty($sfcConfig['sfc_public_key']) && !empty($sfcConfig['sfc_secret_key'])) {
                    $itemDescription = $merchantOrder->items()
                        ->whereNotIn('status_id', [
                            OrderStatus::CANCELLED->value,
                            OrderStatus::RETURN_REQUEST->value,
                            OrderStatus::RETURNED->value,
                            OrderStatus::REFUNDED->value,
                            OrderStatus::UNKNOWN->value
                        ])
                        ->with('product:id,name')
                        ->get()
                        ->map(fn($item) => optional($item->product)->name)
                        ->filter()
                        ->implode(', ');

                    $apiBodyParams = [
                        'invoice' => $merchantOrder->invoice_id ?? $order->invoice_id,
                        'recipient_name' => $order->customer_name,
                        'recipient_phone' => $order->customer_number,
                        'recipient_address' => $order->customer_address,
                        'cod_amount' => $merchantOrder->codAmount(),
                        'item_description' => $itemDescription,
                        'note' => $merchantOrder->notes,
                    ];

                    $response = Http::withHeaders([
                        'api-key' => $sfcConfig['sfc_public_key'],
                        'secret-key' => $sfcConfig['sfc_secret_key'],
                    ])->post($sfcConfig['sfc_base_url'] . '/create_order', $apiBodyParams);

                    if ($response->successful()) {
                        $data = $response->json();
                        $merchantOrder->consignment_id = data_get($data, 'consignment.consignment_id');
                        $merchantOrder->save();
                    }
                }
            }

            if ($newStatus == OrderStatus::DELIVERED->value) {
                $merchantOrder->delivered_at = now();
                $merchantOrder->save();
                if ($merchantOrder->payment && $merchantOrder->payment->payment_method == 'COD') {
                    $merchantOrder->payment->payment_status = PaymentStatus::PAID->value;
                    $merchantOrder->payment->save();
                }
            }

            if ($newStatus == OrderStatus::CANCELLED->value) {
                $merchantOrder->cancel_by = CancelBy::ADMIN->value;
                $merchantOrder->cancelled_by = Auth::id();
                $merchantOrder->cancelled_at = now();
                $merchantOrder->save();
                if ($merchantOrder->payment && $merchantOrder->payment->payment_status == PaymentStatus::PENDING->value) {
                    $merchantOrder->payment->update(['payment_status' => PaymentStatus::CANCELLED->value]);
                }
            }

            DB::commit();

            $admin = Auth::user();
            $activityLog = activity()
                ->performedOn($merchantOrder)
                ->useLog('admin_order_status_change')
                ->causedBy($admin)
                ->withProperties([
                    'status' => OrderStatus::getStatusLabel($newStatus),
                    'admin_name' => $admin->name ?? 'Admin',
                    'admin_phone' => $admin->phone ?? null,
                    'changed_at' => now(),
                ])
                ->log('Admin changed merchant order status');

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
                'data' => [
                    'status_label' => $merchantOrder->fresh()->status_label,
                    'status_bg_color' => $merchantOrder->fresh()->status_bg_color,
                    'activity' => [
                        'created_at' => $activityLog->created_at?->format('d/m/Y h:i A'),
                        'admin_name' => $admin->name ?? 'Admin',
                        'admin_phone' => $admin->phone ?? null,
                        'status' => OrderStatus::getStatusLabel($newStatus),
                        'description' => 'Admin changed merchant order status',
                    ],
                ],
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Something went wrong.',
            ], 500);
        }
    }

    private function validateAdminStatus(int $current, int $next): bool
    {
        if ($current === $next) {
            return false;
        }

        $preRtsStatuses = [
            OrderStatus::PENDING->value,
            OrderStatus::APPROVED->value,
            OrderStatus::PROCESSING->value,
        ];
        if (in_array($current, $preRtsStatuses, true) && in_array($next, $preRtsStatuses, true)) {
            return true;
        }
        if (
            in_array($current, [
                OrderStatus::PENDING->value,
                OrderStatus::APPROVED->value,
                OrderStatus::PROCESSING->value,
            ], true) &&
            $next === OrderStatus::READY_TO_SHIP->value
        ) {
            return true;
        }
        if ($current === OrderStatus::READY_TO_SHIP->value && $next === OrderStatus::DELIVERED->value) {
            return true;
        }
        if (
            ($current === OrderStatus::DELIVERED->value && $next === OrderStatus::CANCELLED->value) ||
            ($current === OrderStatus::CANCELLED->value && $next === OrderStatus::DELIVERED->value)
        ) {
            return true;
        }
        if (
            $current === OrderStatus::PARTIAL_DELIVERED->value &&
            in_array($next, [OrderStatus::DELIVERED->value, OrderStatus::CANCELLED->value], true)
        ) {
            return true;
        }

        return false;
    }

    public function updateShipping(MerchantOrder $merchantOrder, Request $request): JsonResponse
    {
        if ($this->isPayoutLocked($merchantOrder)) {
            return $this->jsonPayoutLockResponse();
        }

        $request->validate([
            'shipping_type' => 'required|string|in:ISD,OSD',
            'shipping_amount' => 'required|numeric|min:0',
            'customer_address' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $oldShippingAmount = $merchantOrder->shipping_amount;
            $newShippingAmount = $request->shipping_amount;

            // Update Merchant Order
            $merchantOrder->update([
                'shipping_amount' => $newShippingAmount,
                'grand_total' => $merchantOrder->sub_total + $newShippingAmount - $merchantOrder->discount_amount,
            ]);

            // Update Main Order
            $order = $merchantOrder->order;
            $order->update([
                'customer_address' => $request->customer_address,
                'shipping_type' => $request->shipping_type,
                'total_shipping_fee' => $order->total_shipping_fee - $oldShippingAmount + $newShippingAmount,
                'grand_total' => $order->sub_total + ($order->total_shipping_fee - $oldShippingAmount + $newShippingAmount) - $order->total_discount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shipping details updated successfully.',
                'data' => [
                    'shipping_amount' => number_format($newShippingAmount, 2),
                    'grand_total' => number_format($merchantOrder->grand_total, 2),
                    'customer_address' => $request->customer_address,
                    'shipping_type' => $request->shipping_type,
                ]
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update shipping details: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function manualDelivery(MerchantOrder $merchantOrder, Request $request)
    {
        if ($this->isPayoutLocked($merchantOrder)) {
            return $this->redirectForPayoutLock($merchantOrder);
        }

        $request->validate([
            'delivery_charge' => 'required|numeric',
            'cod_amount' => 'required|numeric',
            'manually_delivered_note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $orderTimeLine = $merchantOrder->orderTimeLines()->where('status_id', OrderStatus::DELIVERED->value)->first();
            if ($orderTimeLine) {
                $orderTimeLine->update(['date' => now()]);
            }

            $merchantOrder->status_id = OrderStatus::DELIVERED;
            $merchantOrder->fine_amount = max(0, $request->delivery_charge - $merchantOrder->shipping_amount);
            $merchantOrder->delivery_amount_saved = max(0, $merchantOrder->shipping_amount - $request->delivery_charge);
            $merchantOrder->new_cod = $request->cod_amount ?? 0;
            $merchantOrder->courier_status = CourierStatus::DELIVERED;
            $merchantOrder->delivered_at = now();
            $merchantOrder->manually_delivered_note = $request->manually_delivered_note;
            $merchantOrder->manually_delivered_by = Auth::user()->id;
            $merchantOrder->save();

            $merchant = $merchantOrder->merchant;
            $merchant->withdrawal_balance += $merchantOrder->grand_total - ($request->delivery_charge ?? 0) - ($merchantOrder->fine_amount ?? 0);
            $merchant->save();

            $merchantOrder->items()->where('status_id', OrderStatus::READY_TO_SHIP->value)->get()->each(function ($item) {
                $item->status_id = OrderStatus::DELIVERED->value;
                $item->save();
            });

            $orderPayment = $merchantOrder->payment;
            $orderPayment->payment_status = PaymentStatus::PAID->value;
            $orderPayment->save();

            $this->ledgerCreate($merchantOrder);

            DB::commit();

            return to_route('admin.orders.merchant.show', $merchantOrder->invoice_id ?? $merchantOrder->order->invoice_id)->with('success', 'Order updated successfully.');
        } catch (Throwable $th) {
            DB::rollBack();

            return to_route('admin.orders.merchant.show', $merchantOrder->invoice_id ?? $merchantOrder->order->invoice_id)->with('error', 'Something went wrong');
        }
    }

    public function ledgerCreate(MerchantOrder $merchantOrder)
    {
        $calculateTotal = $this->calculateTotal($merchantOrder);
        $uuid = Str::uuid();
        $fineAmount = $merchantOrder->fine_amount;
        $totalAmount = $merchantOrder->total_amount;
        $totalPurchasePrice = $calculateTotal->totalPurchasePrice;
        $totalItemsDiscount = $merchantOrder->item_discount;
        $totalDiscountAmount = $merchantOrder->discount_amount;
        $totalShippingCost = $merchantOrder->shipping_amount;
        $paidShippingCost = $totalShippingCost - $merchantOrder->delivery_amount_saved;
        $totalCommission = $calculateTotal->totalCommission;

        $amountDifference = $totalAmount - $totalPurchasePrice;
        $grossProfit = $amountDifference - $totalItemsDiscount - $totalDiscountAmount;

        $netProfit = $grossProfit - $totalCommission;

        $this->updateAccountBalance($merchantOrder->merchant_id, $totalAmount, AccountTypes::ASSET->value, 'PYRC', 'debit', 'increment', $uuid);
        $this->updateAccountBalance($merchantOrder->merchant_id, $totalAmount, AccountTypes::INCOME->value, 'REVE', 'credit', 'increment', $uuid);
        $this->updateAccountBalance($merchantOrder->merchant_id, $totalPurchasePrice, AccountTypes::INVENTORY->value, 'INAS', 'credit', 'decrement', $uuid);
        $this->updateAccountBalance($merchantOrder->merchant_id, $totalPurchasePrice, AccountTypes::SALE->value, 'COGS', 'debit', 'increment', $uuid);

        if ($totalItemsDiscount > 0) {
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalItemsDiscount, AccountTypes::ASSET->value, 'PYRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalItemsDiscount, AccountTypes::EXPENSE->value, 'PROD', 'debit', 'increment', $uuid);
        }

        if ($totalDiscountAmount > 0) {
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalDiscountAmount, AccountTypes::ASSET->value, 'PYRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalDiscountAmount, AccountTypes::EXPENSE->value, 'PROD', 'debit', 'increment', $uuid);
        }

        if ($totalShippingCost > 0) {
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalShippingCost, AccountTypes::ASSET->value, 'PYRC', 'debit', 'increment', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalShippingCost, AccountTypes::INCOME->value, 'REVE', 'credit', 'increment', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $paidShippingCost, AccountTypes::INCOME->value, 'REVE', 'debit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $paidShippingCost, AccountTypes::ASSET->value, 'PYRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $paidShippingCost, AccountTypes::SALE->value, 'SHPC', 'credit', 'increment', $uuid);
        }

        if ($fineAmount > 0) {
            $this->updateAccountBalance($merchantOrder->merchant_id, $fineAmount, AccountTypes::ASSET->value, 'PYRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $fineAmount, AccountTypes::EXPENSE->value, 'SHFI', 'debit', 'increment', $uuid);
        }

        if ($totalCommission > 0) {
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalCommission, AccountTypes::ASSET->value, 'PYRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalCommission, AccountTypes::EXPENSE->value, 'COMM', 'debit', 'increment', $uuid);
        }

        $this->updateAccountBalance($merchantOrder->merchant_id, $merchantOrder->grand_total, AccountTypes::SALE->value, 'SALE', 'debit', 'increment', $uuid);

        $this->updateAccountBalance($merchantOrder->merchant_id, $netProfit, AccountTypes::SALE->value, 'NETP', 'credit', 'increment', $uuid);
        $this->updateAccountBalance($merchantOrder->merchant_id, $grossProfit, AccountTypes::SALE->value, 'GRPF', 'credit', 'increment', $uuid);
    }

    public function calculateTotal($merchantOrder)
    {
        $totalPurchasePrice = 0;
        $totalCommission = 0;
        $saleProductDetails = $merchantOrder->items()
            ->where('status_id', OrderStatus::DELIVERED->value)
            ->get();

        foreach ($saleProductDetails as $detail) {
            $purchase_price = StockOrder::where(['type' => 2, 'sell_product_detail_id' => $detail->id])->sum('purchase_price');
            $totalCommission += $detail->commission;
            $totalPurchasePrice += $purchase_price;
        }

        return (object) [
            'totalCommission' => $totalCommission,
            'totalPurchasePrice' => $totalPurchasePrice,
        ];
    }

    public function updateAccountBalance($merchantId, $amount, $accountType, $uucode = null, $type = 'credit', $method = 'increment', $uuid = null)
    {
        $account = Account::where('merchant_id', $merchantId)
            ->where('account_type', $accountType)
            ->when($uucode, function ($query, $uucode) {
                $query->where('uucode', $uucode);
            })
            ->first();

        $account->{$method}('balance', $amount);

        MerchantTransaction::create([
            'uuid' => $uuid,
            'merchant_id' => $merchantId,
            'account_id' => $account->id,
            'amount' => $method === 'increment' ? $amount : -$amount,
            'date' => now(),
            'type' => $type,
        ]);
    }

    public function merchantOrderUpdate(Request $request, MerchantOrder $merchantOrder)
    {
        if ($this->isPayoutLocked($merchantOrder)) {
            return $this->redirectForPayoutLock($merchantOrder);
        }

        if (! $this->canEditMerchantOrder($merchantOrder)) {
            return to_route('admin.orders.merchant.show', $merchantOrder->invoice_id ?? $merchantOrder->order->invoice_id)
                ->with('error', 'This merchant order can no longer be edited.');
        }

        try {
            $this->merchantOrderService->update($merchantOrder, $request->all());

            return to_route('admin.orders.merchant.edit', $merchantOrder->invoice_id ?? $merchantOrder->order->invoice_id)->with('success', 'Merchant order updated successfully');
        } catch (Throwable $th) {
            return to_route('admin.orders.merchant.edit', $merchantOrder->invoice_id ?? $merchantOrder->order->invoice_id)->with('error', $th->getMessage() ?: 'Something went wrong');
        }
    }

    private function canEditMerchantOrder(MerchantOrder $merchantOrder): bool
    {
        return in_array($merchantOrder->status_id?->value, [
            OrderStatus::PENDING->value,
            OrderStatus::APPROVED->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::READY_TO_SHIP->value,
        ], true);
    }

    public function orderAddressUpdate(Request $request, MerchantOrder $merchantOrder): RedirectResponse
    {
        if ($this->isPayoutLocked($merchantOrder)) {
            return $this->redirectForPayoutLock($merchantOrder);
        }

        $request->validate([
            'address' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {

            $order = $merchantOrder->order;

            $getDeliveryCharges = $merchantOrder->merchant->getDeliveryCharges();

            if ($order->status_id == OrderStatus::DELIVERED->value) {
                return redirect()->back()->with('error', 'You cannot edit the address of a delivered order');
            }

            $isInsideDhaka = $this->isInsideDhaka($request->address);

            $oldTotalShipping = $order->total_shipping_fee;
            $totalDeliveryCharge = 0;

            foreach ($order->merchantOrders as $mo) {
                $totalMerchantDeliveryCharge = $mo->items->sum(function ($item) use ($isInsideDhaka, $getDeliveryCharges) {
                    return $isInsideDhaka ? $getDeliveryCharges['id_delivery_fee'] : $getDeliveryCharges['od_delivery_fee'];
                });

                $oldMerchantShipping = $mo->shipping_amount;

                $mo->shipping_amount = $totalMerchantDeliveryCharge;
                $mo->grand_total = $mo->grand_total + ($totalMerchantDeliveryCharge - $oldMerchantShipping);
                $mo->save();

                $totalDeliveryCharge += $totalMerchantDeliveryCharge;
            }

            $order->customer_address = $request->address;
            $order->order_address_edited_by = Auth::user()->id;
            $order->shipping_type = $isInsideDhaka ? 'ISD' : 'OSD';
            $order->total_shipping_fee = $totalDeliveryCharge;
            $order->grand_total = $order->grand_total + ($totalDeliveryCharge - $oldTotalShipping);
            $order->save();

            DB::commit();

            return redirect()->back()->with('success', 'Order Address Updated');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function isInsideDhaka($address): bool
    {
        if (preg_match('/[\x80-\xff]/', $address)) {
            $address = banglaToBanglish($address);
        }

        return (new InsideDhakaService)->isInsideDhaka($address);
    }

    public function orderTracking(string $invoiceNumber): JsonResponse
    {
        $sfcConfig = ShopSetting::whereIn('key', ['sfc_base_url', 'sfc_public_key', 'sfc_secret_key'])->pluck('value', 'key')->toArray();

        if (empty($sfcConfig['sfc_base_url']) || empty($sfcConfig['sfc_public_key']) || empty($sfcConfig['sfc_secret_key'])) {
            return ApiResponse::failure('SFC Configuration problem, please contact admin', Response::HTTP_NOT_FOUND);
        }

        $response = Http::withHeaders([
            'api-key' => $sfcConfig['sfc_public_key'],
            'secret-key' => $sfcConfig['sfc_secret_key'],
        ])->get($sfcConfig['sfc_base_url'] . '/trackings_by_invoice/' . $invoiceNumber);

        if (!$response->successful()) {
            return ApiResponse::error('Failed to retrieve order tracking by invoice number ' . $invoiceNumber, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $response->json();

        return ApiResponse::success(
            'Order tracking retrieved successfully.',
            [
                'tracking' => $data['tracking'] ?? [],
            ],
            Response::HTTP_OK
        );
    }

    private function isPayoutLocked(MerchantOrder $merchantOrder): bool
    {
        return ! is_null($merchantOrder->payout_id);
    }

    private function redirectForPayoutLock(MerchantOrder $merchantOrder): RedirectResponse
    {
        return to_route('admin.orders.merchant.show', $merchantOrder->invoice_id ?? $merchantOrder->order->invoice_id)
            ->with('error', 'This merchant order is locked because a payout has already been created.');
    }

    private function jsonPayoutLockResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'This merchant order is locked because a payout has already been created.',
        ], 403);
    }

    private function getInsufficientStockItems(MerchantOrder $merchantOrder): array
    {
        $excludedStatuses = [
            OrderStatus::CANCELLED->value,
            OrderStatus::RETURN_REQUEST->value,
            OrderStatus::RETURNED->value,
            OrderStatus::REFUNDED->value,
            OrderStatus::UNKNOWN->value,
        ];

        $items = $merchantOrder->items()
            ->whereNotIn('status_id', $excludedStatuses)
            ->with([
                'product:id,name,total_stock_qty',
                'product_variant:id,product_id,total_stock_qty',
            ])
            ->get();

        $insufficient = [];

        foreach ($items as $item) {
            $requiredQty = (int) $item->quantity;
            $availableQty = $item->product_variation_id
                ? (int) ($item->product_variant?->total_stock_qty ?? 0)
                : (int) ($item->product?->total_stock_qty ?? 0);

            if ($availableQty < $requiredQty) {
                $name = $item->product?->name ?? 'Item #'.$item->id;
                $insufficient[] = $name." (need {$requiredQty}, available {$availableQty})";
            }
        }

        return $insufficient;
    }

    private function resolveMerchantOrder(string $invoice): MerchantOrder
    {
        return MerchantOrder::where('invoice_id', $invoice)->first()
            ?? MerchantOrder::whereHas('order', fn ($q) => $q->where('invoice_id', $invoice))->firstOrFail();
    }
}
