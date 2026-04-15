<?php

namespace App\Enums;

enum CouponApplyOn: string
{
    case PRODUCT_PRICE = 'product_price';
    case SHIPPING_CHARGE = 'shipping_charge';

    /**
     * Get a human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PRODUCT_PRICE => 'Product Price',
            self::SHIPPING_CHARGE => 'Shipping Charge',
        };
    }
}
