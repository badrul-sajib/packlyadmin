<?php

namespace App\Models\Merchant;

use App\Enums\PayoutRecurringTypes;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantSetting extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    protected $casts = [
        'payout_recurring_type' => PayoutRecurringTypes::class,
    ];
}
