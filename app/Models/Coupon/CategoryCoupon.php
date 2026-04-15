<?php

namespace App\Models\Coupon;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class CategoryCoupon extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';
}
