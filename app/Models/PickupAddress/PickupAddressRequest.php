<?php

namespace App\Models\PickupAddress;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickupAddressRequest extends Model
{
        protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function pickupAddress(): BelongsTo
    {
        return $this->belongsTo(PickupAddress::class);
    }
}
