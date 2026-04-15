<?php

namespace App\Models\Sell;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class SellDetail extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';
}
