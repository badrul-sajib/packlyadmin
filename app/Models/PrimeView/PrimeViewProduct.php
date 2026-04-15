<?php

namespace App\Models\PrimeView;

use App\Models\Merchant\Merchant;
use App\Models\Product\Product;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrimeViewProduct extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $table = 'prime_view_product';

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function prime_view(): BelongsTo
    {
        return $this->belongsTo(PrimeView::class);
    }

    public function primeView(): BelongsTo
    {
        return $this->belongsTo(PrimeView::class);
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new \App\Models\Scopes\SkipDeletedShopProduct);
    }
}
