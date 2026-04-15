<?php

namespace App\Models\Coupon;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class CouponProduct extends Model
{
    use HasTimezone;

    protected $guarded = [];

    protected $connection = 'mysql_internal';
}
