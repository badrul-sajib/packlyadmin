<?php

namespace App\Models\Product;

use App\Enums\ShopProductStatus;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;
use App\Traits\ProductCommission;
use App\Models\Merchant\Merchant;

class ShopProduct extends Model
{
    use HasTimezone, ProductCommission;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['status_label','packly_commission_type'];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:A',
        'updated_at' => 'date:Y-m-d H:i:A',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function active(): bool
    {
        return $this->status == ShopProductStatus::APPROVED->value;
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = ShopProductStatus::label();

        return $labels[(int) $this->status] ?? 'Unknown';
    }

    public function getPacklyCommissionAttribute(): string
    {
        try {
            return $this->getProductCommission($this->product)->commission;
        } catch (\Throwable $th) {
            // Log the exception or handle it as needed
            return "0.00";
        }
    }

    public function getPacklyCommissionTypeAttribute(): string
    {
        try {
            return $this->getProductCommission($this->product)->commission_type;
        } catch (\Throwable $th) {
            // Log the exception or handle it as needed
            return "percent";
        }
    }

    public function shopProductVariations(): Builder|HasMany|ShopProduct
    {
        return $this->hasMany(ShopProductVariation::class, 'product_id', 'product_id');
    }

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new \App\Models\Scopes\SkipDeletedShopProduct);

        static::created(function ($model) {
            Cache::forget('feed_xml_v1');
        });
        static::updated(function ($model) {
            Cache::forget('feed_xml_v1');
        });
        static::deleted(function ($model) {
            Cache::forget('feed_xml_v1');
        });
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
