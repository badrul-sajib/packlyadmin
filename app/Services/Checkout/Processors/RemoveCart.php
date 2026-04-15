<?php

namespace App\Services\Checkout\Processors;

use App\Models\Order\Cart;
use App\Models\Order\CartItem;
use App\Models\Order\Order;

class RemoveCart
{
    public function __construct(private readonly Order $order) {}

    public function __invoke(): void
    {
        $userId = auth()->id();

        $cart = Cart::where('user_id', $userId)->first();
        if (! $cart) {
            return;
        }

        $orderItems = $this->order->orderItemsByMerchant()->get();

        $matches = $orderItems->map(function ($item) {
            return [
                'product_id'           => $item->product_id,
                'product_variation_id' => $item->product_variation_id,
            ];
        });

        foreach ($matches as $match) {
            CartItem::where('cart_id', $cart->id)
                ->where('product_id', $match['product_id'])
                ->where('product_variation_id', $match['product_variation_id'])
                ->delete();
        }
    }
}
