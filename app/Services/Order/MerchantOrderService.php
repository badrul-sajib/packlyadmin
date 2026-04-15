<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Models\Merchant\MerchantOrder;
use App\Services\CustomerOrderService;
use Exception;
use Illuminate\Support\Facades\DB;

class MerchantOrderService
{
    public function recalculateTotals(MerchantOrder $merchantOrder): void
    {
        $order = $merchantOrder->order;
        if (! $order) {
            return;
        }

        $this->recalculateMerchantOrder($merchantOrder, $order->shipping_type);
        $this->recalculateMainOrder($order);
    }

    public function update(MerchantOrder $merchantOrder, array $data)
    {
        DB::transaction(function () use ($merchantOrder, $data) {
            $editableStatuses = [
                OrderStatus::PENDING->value,
                OrderStatus::APPROVED->value,
                OrderStatus::PROCESSING->value,
                OrderStatus::READY_TO_SHIP->value,
            ];

            if (! in_array($merchantOrder->status_id?->value, $editableStatuses, true)) {
                throw new Exception('Only pending/approved/processing/ready-to-ship merchant orders can be edited.');
            }

            $qty = collect($data['qty'] ?? [])
                ->mapWithKeys(fn ($value, $key) => [(int) $key => (int) $value]);
            $removeItemIds = collect($data['remove_item_ids'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $order = $merchantOrder->order;
            $pendingItems = $merchantOrder->orderItems()
                ->whereIn('status_id', $editableStatuses)
                ->get()
                ->keyBy('id');

            if ($pendingItems->isEmpty()) {
                throw new Exception('No editable items found for this merchant order.');
            }

            foreach ($qty as $itemId => $newQuantity) {
                if (! $pendingItems->has($itemId)) {
                    throw new Exception("Item #{$itemId} is not editable.");
                }
                if ($newQuantity < 1) {
                    throw new Exception("Quantity for item #{$itemId} must be at least 1.");
                }
            }

            foreach ($removeItemIds as $itemId) {
                if (! $pendingItems->has($itemId)) {
                    throw new Exception("Item #{$itemId} is not removable.");
                }
            }

            $remainingPendingItems = $pendingItems->keys()->diff($removeItemIds);
            if ($remainingPendingItems->count() < 1) {
                throw new Exception('At least one pending item must remain in the order.');
            }

            foreach ($removeItemIds as $itemId) {
                $pendingItems[$itemId]->delete();
            }

            $before = [
                'total_items' => (int) $merchantOrder->total_items,
                'total_amount' => (float) $merchantOrder->total_amount,
                'sub_total' => (float) $merchantOrder->sub_total,
                'discount_amount' => (float) $merchantOrder->discount_amount,
                'shipping_amount' => (float) $merchantOrder->shipping_amount,
                'grand_total' => (float) $merchantOrder->grand_total,
            ];

            $updatedPendingItems = $merchantOrder->orderItems()
                ->whereIn('status_id', $editableStatuses)
                ->get();

            $updatedPendingItems->each(function ($item) use ($qty) {
                if (! $qty->has($item->id)) {
                    return;
                }

                $oldQuantity = (int) $item->quantity;
                $newQuantity = (int) $qty[$item->id];

                if ($oldQuantity === $newQuantity) {
                    return;
                }

                $perUnitCommission = $oldQuantity > 0 ? ((float) $item->commission / $oldQuantity) : 0;
                $perUnitCouponDiscount = $oldQuantity > 0 ? ((float) ($item->coupon_discount ?? 0) / $oldQuantity) : 0;

                $item->quantity = $newQuantity;
                $item->commission = round($perUnitCommission * $newQuantity, 2);
                $item->coupon_discount = round($perUnitCouponDiscount * $newQuantity, 2);
                $item->save();
            });

            $manualShipping = isset($data['shipping_amount']) && is_numeric($data['shipping_amount'])
                ? (float) $data['shipping_amount']
                : null;

            $this->recalculateMerchantOrder($merchantOrder->fresh(), $order->shipping_type, $manualShipping);
            $this->recalculateMainOrder($order->fresh());

            $latestMerchantOrder = $merchantOrder->fresh();
            $after = [
                'total_items' => (int) $latestMerchantOrder->total_items,
                'total_amount' => (float) $latestMerchantOrder->total_amount,
                'sub_total' => (float) $latestMerchantOrder->sub_total,
                'discount_amount' => (float) $latestMerchantOrder->discount_amount,
                'shipping_amount' => (float) $latestMerchantOrder->shipping_amount,
                'grand_total' => (float) $latestMerchantOrder->grand_total,
            ];

            $activity = activity()
                ->useLog('merchant-order-edit')
                ->event('updated')
                ->performedOn($latestMerchantOrder)
                ->withProperties([
                    'before' => $before,
                    'after' => $after,
                    'updated_qty' => $qty->all(),
                    'removed_item_ids' => $removeItemIds->all(),
                ]);

            if (auth()->check()) {
                $activity->causedBy(auth()->user());
            }

            $activity->log('Merchant order edited');
        });
    }

    private function recalculateMerchantOrder(MerchantOrder $merchantOrder, string $shippingType, ?float $manualShippingAmount = null): void
    {
        $activeItems = $merchantOrder->orderItems()
            ->where('status_id', '!=', OrderStatus::CANCELLED->value)
            ->get();

        $totalItems = (int) $activeItems->sum('quantity');
        $totalAmount = (float) $activeItems->sum(fn ($item) => $item->regular_price * $item->quantity);
        $subTotal = (float) $activeItems->sum(fn ($item) => $item->price * $item->quantity);
        $itemDiscount = $totalAmount - $subTotal;
        $couponDiscount = (float) $activeItems->sum('coupon_discount');

        if ($manualShippingAmount !== null) {
            $shippingAmount = $manualShippingAmount;
        } elseif ($totalItems > 0) {
            $shippingAmount = CustomerOrderService::recalculateShipping($merchantOrder, $shippingType);
        } else {
            $shippingAmount = 0;
        }

        $merchantOrder->update([
            'total_items' => $totalItems,
            'total_amount' => $totalAmount,
            'sub_total' => $subTotal,
            'item_discount' => $itemDiscount,
            'discount_amount' => $couponDiscount,
            'shipping_amount' => $shippingAmount,
            'grand_total' => max(0, $subTotal + $shippingAmount - $couponDiscount),
        ]);
    }

    private function recalculateMainOrder($order): void
    {
        $merchantOrders = $order->merchantOrders()->get();

        $totalItems = (int) $merchantOrders->sum('total_items');
        $totalAmount = (float) $merchantOrders->sum('total_amount');
        $subTotal = (float) $merchantOrders->sum('sub_total');
        $itemDiscount = (float) $merchantOrders->sum('item_discount');
        $totalDiscount = (float) $merchantOrders->sum('discount_amount');
        $sumMerchantShipping = (float) $merchantOrders->sum('shipping_amount');

        $existingShippingOffset = max(0, $sumMerchantShipping - (float) $order->total_shipping_fee);
        $totalShippingFee = max(0, $sumMerchantShipping - $existingShippingOffset);

        $order->update([
            'total_items' => $totalItems,
            'total_amount' => $totalAmount,
            'sub_total' => $subTotal,
            'item_discount' => $itemDiscount,
            'total_discount' => $totalDiscount,
            'total_shipping_fee' => $totalShippingFee,
            'grand_total' => max(0, $subTotal + $totalShippingFee - $totalDiscount),
        ]);
    }
}
