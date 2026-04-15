<?php

namespace App\Models\Product;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class ShopProductVariation extends Model
{
    use HasTimezone;

    protected $guarded = [];

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}
