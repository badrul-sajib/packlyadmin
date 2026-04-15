<?php

namespace App\Services\Checkout\Validators;

use App\DTOs\CheckoutData;
use App\Models\Order\Order;
use Illuminate\Validation\ValidationException;

class PreventDuplicateOrder
{
    public function preventDuplicateOrder(CheckoutData $data)
    {
        $recentOrder = Order::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subMinutes(5))
            ->latest()
            ->first();

        if (! $recentOrder) {
            return;
        }

        // Get last order items
        $previousItems = $recentOrder->orderItems()
            ->select('product_id', 'quantity')
            ->orderBy('product_id')
            ->get()
            ->map(fn ($item) => [
                'product_id' => (int) $item->product_id,
                'quantity'   => (int) $item->quantity,
            ])
            ->toArray();

        // Current request items
        $currentItems = collect($data->productIds)
            ->map(fn ($id, $i) => [
                'product_id' => (int) $id,
                'quantity'   => (int) $data->quantities[$i] ?? 0,
            ])
            ->sortBy('product_id')
            ->values()
            ->toArray();

        $sameItems = $previousItems == $currentItems;
        $samePrice = (int) $recentOrder->grand_total === (int) $data->final_payable_price;

        if ($sameItems && $samePrice) {
            throw ValidationException::withMessages([
                'duplicate_order' => 'You have placed the same order recently. Please try again in 5 minutes.',
            ]);

        }
    }
}
