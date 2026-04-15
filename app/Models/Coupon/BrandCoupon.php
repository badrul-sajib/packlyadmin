<?php

namespace App\Models\Coupon;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class BrandCoupon extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';
}
