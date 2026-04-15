<?php

namespace App\Models\Coupon;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class CouponUsageProduct extends Model
{
    use HasTimezone;

    protected $table = 'coupon_usage_product';

    protected $connection = 'mysql_internal';
}
