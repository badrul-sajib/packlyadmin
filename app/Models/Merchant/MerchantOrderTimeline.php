<?php

namespace App\Models\Merchant;

use App\Enums\OrderStatusTimelineTypes;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class MerchantOrderTimeline extends Model
{
    use HasTimezone;

    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime',
        'type' => OrderStatusTimelineTypes::class,
    ];
}
