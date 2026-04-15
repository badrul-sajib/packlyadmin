<?php

namespace App\Models\Product;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BadgeProduct extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    /**
     * Get the badge associated with the badge product.
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    /**
     * Get the product associated with the badge product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variations associated with the badge product.
     */
    public function badgeProductVariations(): HasMany
    {
        return $this->hasMany(BadgeProductVariation::class, 'badge_product_id');
    }

}
