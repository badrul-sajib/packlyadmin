<?php

namespace App\Models\Product;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
