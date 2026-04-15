<?php

namespace App\Models\Sell;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellPayment extends Model
{
    use HasTimezone;

    protected $guarded = ['id'];

    public function sellProduct(): BelongsTo
    {
        return $this->belongsTo(SellProduct::class, 'sell_id');
    }

    public function sellPaymentDetails(): HasMany|Builder|SellPayment
    {
        return $this->hasMany(SellPaymentDetail::class, 'sell_payment_id');
    }
}
