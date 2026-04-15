<?php

namespace App\Models\Payment;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class PaidBySfc extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    public $timestamps = false;

    protected $guarded = [];

        public $casts = [
        'created_at' => 'datetime',
        'ready_at' => 'datetime',
        'paid_at' => 'datetime',
    ];
}
