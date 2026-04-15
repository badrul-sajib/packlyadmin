<?php

namespace App\Models\Product;

use Exception;
use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Unit\Unit;
use App\Models\User\User;
use App\Traits\HasDrafts;
use App\Models\Brand\Brand;
use App\Traits\HasTimezone;
use App\Enums\MerchantStatus;
use App\Models\Review\Review;
use App\Models\Order\OrderItem;
use App\Enums\ShopProductStatus;
use App\Models\Shop\ShopProduct;
use App\Models\Category\Category;
use App\Models\Merchant\Merchant;
use App\Models\Coupon\CouponUsage;
use Illuminate\Support\Facades\DB;
use App\Models\PrimeView\PrimeView;
use App\Models\Category\SubCategory;
use App\Models\Stock\StockInventory;
use App\Enums\WarrantyRecurringTypes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use App\Models\Category\SubCategoryChild;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Variation\VariationAttribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Merchant\MerchantProductCommission;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model implements Mediable
{
    use HasMedia, HasTimezone, SoftDeletes,HasDrafts;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $hidden = ['media', 'reviews', 'pivot'];

    protected $appends = ['image', 'is_variant', 'thumbnail', 'rating_avg', 'rating_count'];

    protected $casts = [
        'warranty_recurring_type' => WarrantyRecurringTypes::class,
        'created_at'              => 'datetime:Y M D/d H:i A',
    ];

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function getDraftableFields(): array
    {
        return $this->draftable;
    }

    public static int $PRODUCT_TYPE_SINGLE = 1;

    public static int $PRODUCT_TYPE_VARIANT = 2;

    public static int $SELLING_TYPE_RETAIL = 1;

    public static int $SELLING_TYPE_WHOLESALE = 2;

    public static int $SELLING_TYPE_BOTH = 3;


    // -------------------------- Relationships -------------------#

    public function shopProduct(): Product|HasOne|Builder
    {
        return $this->hasOne(ShopProduct::class);
    }

    public function comments(): Product|Builder|HasMany
    {
        return $this->hasMany(ProductComment::class, 'product_id');
    }

    public function primeViews(): BelongsToMany
    {
        return $this->belongsToMany(PrimeView::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function productDetail(): Product|HasOne|Builder
    {
        return $this->hasOne(ProductDetails::class, 'product_id', 'id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function subCategoryChild(): BelongsTo
    {
        return $this->belongsTo(SubCategoryChild::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function reviews(): Product|Builder|HasMany
    {
        return $this->hasMany(Review::class);
    }


    public function variations(): Product|Builder|HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function attributes(): Builder|HasMany|Product
    {
        return $this->hasMany(\App\Models\Attribute\VariationAttribute::class);
    }

    public function variationAttributes(): Product|Builder|HasManyThrough
    {
        return $this->hasManyThrough(
            VariationAttribute::class,
            ProductVariation::class,
            'product_id', // Foreign key on `product_variations` table
            'product_variation_id', // Foreign key on `variation_attributes` table
            'id', // Local key on `products` table
            'id' // Local key on `product_variations` table
        );
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'badge_products', 'product_id', 'badge_id')->where('status', 1);
    }

    // duplicate because of commission is old method
    public function commission(): BelongsTo
    {
        return $this->belongsTo(MerchantProductCommission::class, 'id', 'product_id');
    }

    public function merchantCommission(): BelongsTo
    {
        return $this->belongsTo(MerchantProductCommission::class, 'id', 'product_id');
    }


    /**
     * Get the badge products associated with the product.
     */
    public function badgeProducts(): Product|Builder|HasMany
    {
        return $this->hasMany(BadgeProduct::class, 'product_id');
    }

    public function badgeProductVariations(): Product|Builder|HasManyThrough
    {
        return $this->hasManyThrough(
            BadgeProductVariation::class,
            BadgeProduct::class,
            'product_id',  // Foreign key on badge_products table
            'badge_product_id', // Foreign key on badge_product_variations table
            'id', // Local key on products table
            'id'  // Local key on badge_products table
        );
    }

    public function stockInventories(): HasMany
    {
        return $this->hasMany(StockInventory::class);
    }

    public function shopProducts(): Product|Builder|HasMany
    {
        return $this->hasMany(ShopProduct::class);
    }

    public function couponUsages(): BelongsToMany
    {
        return $this->belongsToMany(CouponUsage::class, 'coupon_usage_product'); // If you add timestamps to the pivot table
    }

    // -------------------------- Accessors and Mutators ----------#

    public function setImageAttribute($file): void
    {
        if ($file) {
            $this->addMedia($file, 'images', ['tags' => '']);
        }
    }

    public function getImageAttribute(): array
    {
        // check url in ecommer or invontory

        return $this->getUrl('images', config('app.url'));
    }

    public function getRatingAvgAttribute(): float
    {
        return round($this->total_rating ?? 0, 2);
    }

    public function getRatingCountAttribute(): int
    {
        return $this->total_review ?? 0;
    }

    public function setThumbnailAttribute($file): void
    {
        if ($file) {
            $this->deleteMediaCollection('thumbnail');

            $this->addMedia($file, 'thumbnail');
        }
    }

    public function getThumbnailAttribute(): string
    {
        return $this->getFirstUrl('thumbnail', config('app.url'));
    }

    public function getIsVariantAttribute(): bool
    {
        return ! ($this->product_type_id == 1);
    }

    public function getSpecificationAttribute(): string
    {
        return $this->attributes['specification'] ?? '';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }


    // -------------------------- Scopes --------------------------#

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 1)
            ->whereHas('shopProduct', function ($query) {
                $query->where('status', ShopProductStatus::APPROVED->value);
                $query->where('active_status', '1');
            })
            ->whereHas('merchant', function ($query) {
                $query->where('shop_status', MerchantStatus::Active->value);
            })
            ->where('total_stock_qty', '>', 0);

    }

    public function scopeBaseShopQuery($query)
    {
        return $query->select([
                'products.*',
                'sp.product_id',
                'sp.e_price as e_price',
                'sp.e_discount_price as e_discount_price',
                'sp.status',
                'merchants.name as merchant_name',
                'merchants.shop_name as shop_name',
                'brands.name as brand_name',
                'sub_categories.name as sub_category_name',
                'child_category.name as child_category_name',
                'categories.id as category_id',
                'categories.name as category_name',
                'categories.slug as category_slug',
                'media.file_path as thumbnail_path',
            ])
            ->where('products.status', 1)
            ->join('merchants', 'merchants.id', '=', 'products.merchant_id')
            ->join('shop_products as sp', function ($q) {
                $q->on('sp.product_id', '=', 'products.id')
                ->where('sp.status', 2)
                ->where('sp.e_discount_price', '>', 0)
                ->where('sp.e_price', '>', 0);
            })
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('sub_categories', 'sub_categories.id', '=', 'products.sub_category_id')
            ->leftJoin('sub_category_children as child_category', 'child_category.id', '=', 'products.sub_category_child_id')
            ->leftJoin('media', function ($q) {
                $q->on('media.model_id', '=', 'products.id')
                ->where('media.model_type', '=', 'App\Models\Product\Product')
                ->where('media.collection_name', 'thumbnail');
            });

    }


    // -------------------------- Methods -------------------------#


    public static function generateUnique12DigitBarcode(): string
    {
        do {
            $barcode = (string) mt_rand(100000000000, 999999999999);
        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }

    public static function currentProductStock(int $productId, ?int $variationId): int
    {
        if ($variationId) {
            $variation = ProductVariation::where('id', $variationId)->where('product_id', $productId)->first();
            if (! $variation) {
                return 0;
            }

            return $variation->total_stock_qty ?? 0;
        }

        $product = Product::find($productId);

        if (! $product) {
            return 0;
        }

        return $product->total_stock_qty;
    }

    public function productWarranty()
    {
        return $this->hasOne(ProductWarranty::class);
    }

    public function orders()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }

    public function updateRating()
    {
        $avg                = (float) ($this->reviews()->avg('rating') ?? 0);
        $this->total_rating = round($avg, 2);
        $this->total_review = $this->reviews()->count() ?? 0;
        $this->save();
    }

    // -------------------------- Static --------------------------#

    public static function boot()
    {
        parent::boot();
        static::created(function ($model) {
            Cache::forget('feed_xml_v1');
            Cache::forget('frontend-categories');
            \App\Jobs\IndexProduct::dispatch($model->id);
        });
        static::updated(function ($model) {
            Cache::forget('feed_xml_v1');
            Cache::forget('frontend-categories');
            \App\Jobs\IndexProduct::dispatch($model->id);
        });
        static::deleted(function ($model) {
            Cache::forget('feed_xml_v1');
            Cache::forget('frontend-categories');
        });
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }


}
