<?php

namespace App\Models\Warehouse;

use App\Models\Merchant\Merchant;
use App\Models\Purchase\Purchase;
use App\Models\Stock\StockOrder;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function purchases(): Warehouse|Builder|HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function stockOrders(): Warehouse|Builder|HasMany
    {
        return $this->hasMany(StockOrder::class);
    }
}
