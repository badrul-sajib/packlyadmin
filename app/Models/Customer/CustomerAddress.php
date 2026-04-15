<?php

namespace App\Models\Customer;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';
}
