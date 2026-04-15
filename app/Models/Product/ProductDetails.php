<?php

namespace App\Models\Product;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDetails extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function selectedVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'default_variation_id', 'id');
    }
}
