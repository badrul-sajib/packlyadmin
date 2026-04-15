<?php

namespace App\Models\Payment;

use App\Models\Order\Order;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SslcommerzPayment extends Model
{
    use HasTimezone;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
