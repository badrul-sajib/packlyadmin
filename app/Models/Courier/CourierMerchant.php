<?php

namespace App\Models\Courier;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierMerchant extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $table = 'courier_merchant';

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }
}
