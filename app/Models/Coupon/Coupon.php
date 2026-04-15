<?php

namespace App\Models\Coupon;

use Carbon\Carbon;
use App\Enums\CommonType;
use App\Models\User\User;
use App\Models\Brand\Brand;
use App\Traits\HasTimezone;
use App\Enums\CouponApplyOn;
use App\Models\Product\Product;
use App\Models\Category\Category;
use App\Models\Merchant\Merchant;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['campaign_status'];

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function merchants(): BelongsToMany
    {
        return $this->belongsToMany(Merchant::class, 'coupon_merchants');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_coupons');
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'brand_coupons');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_products');
    }

    public function productVariants(): Builder|HasMany|Coupon
    {
        return $this->hasMany(CouponProductVariant::class, 'coupon_id', 'id');
    }

    public function couponUsages(): Builder|HasMany|Coupon
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id', 'id');
    }

    public function getTypeLabel($filed): string
    {
        return CommonType::label()[$this->$filed->value];
    }

    public function getCampaignStatusAttribute(): string
    {
        if ($this->status === 'pending') {
            return 'pending';
        }

        $now       = Carbon::now();
        $startDate = Carbon::parse($this->start_date)->startOfDay();
        $endDate   = Carbon::parse($this->end_date)->endOfDay();

        if ($now->lt($startDate)) {
            return 'upcoming';
        } elseif ($now->gt($endDate)) {
            return 'expired';
        }

        return 'running';
    }

    protected $casts = [
        'product_type'  => CommonType::class,
        'category_type' => CommonType::class,
        'brand_type'    => CommonType::class,
        'merchant_type' => CommonType::class,
    ];
}
