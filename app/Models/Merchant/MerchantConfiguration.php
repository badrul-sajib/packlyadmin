<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class MerchantConfiguration extends Model
{
    protected $fillable = [
        'merchant_id',
        'min_amount',
        'per_day_request',
        'payout_charge',
        'maximum_product_request',
        'commission_rate',
        'payout_request_date',
        'id_delivery_fee',
        'od_delivery_fee',
        'ed_delivery_fee',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
