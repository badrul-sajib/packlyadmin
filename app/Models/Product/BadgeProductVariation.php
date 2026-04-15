<?php

namespace App\Models\Product;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeProductVariation extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $fillable = ['badge_product_id', 'product_variation_id'];

    public function badgeProduct(): BelongsTo
    {
        return $this->belongsTo(BadgeProduct::class, 'badge_product_id');
    }

    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}
