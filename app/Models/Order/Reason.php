<?php

namespace App\Models\Order;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reason extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function orderItemCases(): Reason|Builder|HasMany
    {
        return $this->hasMany(OrderItemCase::class);
    }
}
