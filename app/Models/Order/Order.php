<?php

namespace App\Models\Order;

use App\Models\User\User;
use App\Models\SpamAttempt;
use App\Traits\HasTimezone;
use App\Models\Coupon\Coupon;
use App\Models\Coupon\CouponUsage;
use App\Models\Merchant\MerchantOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Order extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = ['id'];

    public static string $ORDER_FROM_APP = '1';

    public static string $ORDER_FROM_WEB = '2';

    public function merchantOrders(): Builder|HasMany|Order
    {
        return $this->hasMany(MerchantOrder::class);
    }

    public function orderItems(): Builder|HasManyThrough|Order
    {
        return $this->hasManyThrough(OrderItem::class, MerchantOrder::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function couponUsage(): HasOne
    {
        return $this->hasOne(CouponUsage::class, 'order_id', 'id');
    }

    public function customer_location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'customer_location_id', 'id');
    }

    public function orderItemsByMerchant(): Builder|HasManyThrough|Order
    {
        return $this->hasManyThrough(OrderItem::class, MerchantOrder::class);
    }

    public function scopeNotSpam(Builder $query): Builder
    {
        return $query->where('is_spam', false)->orWhereNull('is_spam');
    }
    public function scopeIsSpam(Builder $query): Builder
    {
        return $query->where('is_spam', true);
    }

    public function spamAttempt(): HasOne 
    {
        return $this->hasOne(SpamAttempt::class);    
    }
   
}
