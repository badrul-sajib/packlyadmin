<?php

namespace App\Models\Sell;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class SellType extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
