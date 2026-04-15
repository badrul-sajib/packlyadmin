<?php

namespace App\Models\Supplier;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];
}
