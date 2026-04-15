<?php

namespace App\Models\Coupon;

use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Models\Variation\VariationAttribute;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CouponProductVariant extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id', 'id');
    }

    public function variations(): HasManyThrough
    {
        return $this->hasManyThrough(
            VariationAttribute::class,
            ProductVariation::class,
            'id',
            'product_variation_id',
            'product_variation_id',
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
