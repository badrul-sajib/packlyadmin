<?php

namespace App\Models\Unit;

use App\Models\Product\Product;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $fillable = ['name', 'merchant_id', 'slug', 'added_by', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
