<?php

namespace App\Models\Sell;

use App\Models\Account\Account;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellPaymentDetail extends Model
{
    use HasTimezone;

    protected $guarded = ['id'];

    public function sellPayment(): BelongsTo
    {
        return $this->belongsTo(SellPayment::class, 'sell_payment_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
