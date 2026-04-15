<?php

namespace App\Models\Stock;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOrder extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function stockInventory(): BelongsTo
    {
        return $this->belongsTo(StockInventory::class);
    }
}
