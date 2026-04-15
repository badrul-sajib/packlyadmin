<?php

namespace App\Models\Voucher;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];
}
