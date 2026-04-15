<?php

namespace App\Models\Coupon;

use App\Models\Order\Order;
use App\Traits\HasTimezone;
use App\Models\Product\Product;
use App\Models\Merchant\MerchantOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CouponUsage extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_usage_product');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function merchantOrder()
    {
        return $this->belongsTo(MerchantOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User\User::class);
    }
}
