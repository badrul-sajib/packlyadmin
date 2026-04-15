<?php

namespace App\Models\Shop;

use App\Enums\MerchantStatus;
use App\Enums\ProductAvailabilityStatus;
use App\Enums\ShopProductStatus;
use App\Models\Merchant\Merchant;
use App\Models\Product\Product;
use App\Models\Product\ProductHoldStatus;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class ShopProduct extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['status_label', 'status_color'];

    public array $cast = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    // ProductHoldStatus
    public function productHoldStatus(): ShopProduct|HasOne|Builder
    {
        return $this->hasOne(ProductHoldStatus::class, 'shop_product_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', ShopProductStatus::APPROVED);
    }

    public function scopeActiveStatus($query)
    {
        return $query->where('active_status', 1);
    }

    public function getStatusLabelAttribute(): string
    {
        $status = ShopProductStatus::label();

        return $status[$this->status];
    }

    public function getStatusColorAttribute(): string
    {
        $status = ShopProductStatus::status_by_color();

        return $status[$this->status];
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function isAvailable(?int $variationId = null): bool
    {
        $activeStatusCheck = $this->active_status == '1'                                &&
            $this->status                         == ShopProductStatus::APPROVED->value &&
            $this->merchant->shop_status->value   == MerchantStatus::Active->value;

        $stockCheck = $variationId ? $this->product->variations()->where('id', $variationId)->where('total_stock_qty', '>', 0)->exists() : $this->product->total_stock_qty > 0;

        return $activeStatusCheck && $stockCheck;
    }

    public function availabilityStatus(?int $variationId = null): ProductAvailabilityStatus
    {
        // Check shop status
        if ($this->merchant->shop_status->value != MerchantStatus::Active->value) {
            return ProductAvailabilityStatus::SHOP_INACTIVE;
        }

        // check product status
        if ($this->product?->status != '1' || $this->product->variations()->where('id', $variationId)->where('status', '0')->exists()) {
            return ProductAvailabilityStatus::PRODUCT_INACTIVE;
        }

        // Check Shop product status
        if ($this->active_status != '1' || $this->status != ShopProductStatus::APPROVED->value) {
            return ProductAvailabilityStatus::PRODUCT_INACTIVE;
        }

        // Check stock
        $hasStock = $variationId
            ? $this->product->variations()
                ->where('id', $variationId)
                ->where('total_stock_qty', '>', 0)
                ->exists()
            : $this->product->total_stock_qty > 0;

        if (! $hasStock) {
            return ProductAvailabilityStatus::STOCK_OUT;
        }

        // If all good
        return ProductAvailabilityStatus::AVAILABLE;
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

        static::created(fn ($model) => self::syncCategoryHasProducts($model));
        static::updated(fn ($model) => self::syncCategoryHasProducts($model));
        static::deleted(fn ($model) => self::syncCategoryHasProducts($model));
    }

    private static function syncCategoryHasProducts($model)
    {
        Cache::forget('frontend-categories');

        if (! $product = $model->product) {
            return;
        }

        $map = [
            'categories'             => $product->category_id,
            'sub_categories'         => $product->sub_category_id,
            'sub_category_children'  => $product->sub_category_child_id,
        ];

        foreach ($map as $table => $categoryId) {
            if (! $categoryId) {
                continue;
            }

            $column = match ($table) {
                'categories'            => 'category_id',
                'sub_categories'        => 'sub_category_id',
                'sub_category_children' => 'sub_category_child_id',
            };

            $has = DB::table('shop_products')
                ->join('products', 'products.id', '=', 'shop_products.product_id')
                ->where("products.{$column}", $categoryId)
                ->where('products.deleted_at', null)
                ->where('shop_products.status', ShopProductStatus::APPROVED->value)
                ->where('shop_products.active_status', 1)
                ->exists();

            DB::table($table)
                ->where('id', $categoryId)
                ->update(['has_products' => $has]);
        }
    }
}
