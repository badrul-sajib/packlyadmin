<?php

namespace App\Models\Shop;

use App\Models\Merchant\Merchant;
use Illuminate\Database\Eloquent\Model;

class PopularShop extends Model
{
    protected $guarded = [];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
