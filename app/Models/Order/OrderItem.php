<?php

namespace App\Models\Order;

use App\Enums\OrderStatus;
use App\Models\Merchant\MerchantOrder;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Models\Review\Review;
use App\Models\User\User;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['status_label', 'status_bg_color'];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(MerchantOrder::class, 'merchant_order_id', 'id');
    }

    public function merchantOrder(): BelongsTo
    {
        return $this->belongsTo(MerchantOrder::class, 'merchant_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function product_variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id', 'id');
    }

    public function itemCase(): HasOne
    {
        return $this->hasOne(OrderItemCase::class, 'order_item_id', 'id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'order_item_id', 'id');
    }

    public function getStatusLabelAttribute()
    {
        $statuses = OrderStatus::getProductStatusLabels() ?? [];

        return $statuses[$this->status_id] ?? 'Unknown';
    }

    public function getStatusBgColorAttribute()
    {
        $statuses = OrderStatus::status_by_color() ?? [];

        return $statuses[$this->status_id] ?? 'alert-warning';
    }

    public function commissionPercentage(): float|int
    {
        if ($this->price > 0) {
            $percentage = ($this->commission / $this->price) * 100;

            return round($percentage / $this->quantity, 2);
        }

        return 0;
    }

    public function cases(): OrderItem|Builder|HasMany
    {
        return $this->hasMany(OrderItemCase::class);
    }

    public function actionBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
