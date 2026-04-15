<?php

namespace App\Models\Purchase;

use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseDetail extends Model
{
    use HasTimezone, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    // Relationship with Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with ProductVariation
    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id', 'id');
    }
}
