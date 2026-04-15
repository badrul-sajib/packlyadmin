<?php

namespace App\Models\Sell;

use App\Models\Product\Product;
use App\Models\Stock\StockOrder;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellProductDetail extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stock_order(): BelongsTo
    {
        return $this->belongsTo(StockOrder::class, 'id', 'sell_product_detail_id');
    }
}
