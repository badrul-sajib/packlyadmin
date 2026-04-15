<?php

namespace App\Models\Payment;

use App\Models\Merchant\MerchantOrder;
use App\Models\Order\OrderPayment;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class EPayment extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function orderPayments(): HasManyThrough
    {
        return $this->hasManyThrough(
            OrderPayment::class,     // Final target model
            MerchantOrder::class,    // Intermediate model
            'order_id',              // Foreign key on MerchantOrder
            'merchant_order_id',     // Foreign key on OrderPayment
            'order_id',              // Local key on EPayment
            'id'                     // Local key on MerchantOrder
        );
    }
}
