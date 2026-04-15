<?php

namespace App\Models\Order;

use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasTimezone;

    protected $guarded = [];

    protected $connection = 'mysql_internal';

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function getRegularPriceAttribute()
    {
        return $this->product_variation_id
            ? $this->variation?->shopVariation?->e_price ?? 0
            : $this->product?->shopProduct?->e_price     ?? 0;
    }

    public function getDiscountedPriceAttribute()
    {
        return $this->product_variation_id
            ? $this->variation?->shopVariation?->e_discount_price ?? 0
            : $this->product?->shopProduct?->e_discount_price     ?? 0;
    }

    public function getIdDeliveryFeeAttribute()
    {
        return $this->product_variation_id
            ? $this->variation?->shopVariation?->id_delivery_fee ?? 0
            : $this->product?->shopProduct?->id_delivery_fee     ?? 0;
    }

    public function getOdDeliveryFeeAttribute()
    {
        return $this->product_variation_id
            ? $this->variation?->shopVariation?->od_delivery_fee ?? 0
            : $this->product?->shopProduct?->od_delivery_fee     ?? 0;
    }

    public function getEdDeliveryFeeAttribute()
    {
        return $this->product_variation_id
            ? $this->variation?->shopVariation?->ed_delivery_fee ?? 0
            : $this->product?->shopProduct?->ed_delivery_fee     ?? 0;
    }
}
