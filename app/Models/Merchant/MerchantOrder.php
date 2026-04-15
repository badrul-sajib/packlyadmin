<?php

namespace App\Models\Merchant;

use App\Enums\CancelBy;
use App\Enums\CourierStatus;
use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutRequestStatus;
use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderPayment;
use App\Models\Payment\Payout;
use App\Models\User\User;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MerchantOrder extends Model
{
    use HasTimezone;

    protected $guarded = [];

    protected $connection = 'mysql_internal';

    protected $appends = ['status_label', 'status_bg_color', 'payment_status_label', 'payment_status_bg_color'];

    public $casts = [
        'delivery_type' => DeliveryType::class,
        'status_id' => OrderStatus::class,
        'courier_status' => CourierStatus::class,
        'cancel_by' => CancelBy::class,
        'cancelled_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'merchant_order_id', 'id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(OrderPayment::class);
    }

    public function getStatusLabelAttribute()
    {
        $statuses = OrderStatus::getStatusLabels() ?? [];

        return $statuses[$this->status_id?->value] ?? 'Unknown';
    }

    public function getPaymentStatusLabelAttribute()
    {
        $statuses = PaymentStatus::getStatusLabels() ?? [];

        return $statuses[$this->payment?->payment_status] ?? 'Unknown';
    }

    public function getStatusBgColorAttribute()
    {
        $statuses = OrderStatus::status_by_color() ?? [];

        return $statuses[$this->status_id?->value] ?? 'alert-warning';
    }

    public function getPaymentStatusBgColorAttribute()
    {
        $statuses = PaymentStatus::status_by_color() ?? [];

        return $statuses[$this->payment?->payment_status] ?? 'warning';
    }

    public function scopeUserOrders($query)
    {
        return $query->whereHas('order', function ($query) {
            $query->where('user_id', auth()->user()->id);
        });
    }

    public function orderTimeLines(): HasMany
    {
        return $this->hasMany(MerchantOrderTimeline::class);
    }

    public function payouts(): BelongsToMany
    {
        return $this->belongsToMany(Payout::class, 'merchant_order_payout');
    }

    public function gatewayCharge(): float
    {
        $payment = $this->payment()
            ->where('payment_status', PaymentStatus::PAID->value)
            ->first();

        if (
            !$payment ||
            $payment->payment_method !== 'SSLCommerz' ||
            !is_numeric($this->sub_total)
        ) {
            return 0;
        }

        return ($this->sub_total * getGatewayCharge()) / 100;
    }

    public function codAmount(): float
    {
        $payment = $this->payment()
            ->where('payment_status', PaymentStatus::PAID->value)
            ->first();

        if ($payment && $payment->payment_method === 'SSLCommerz') {
            return 0;
        }

        return (float) ($this->grand_total ?? 0);
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function payoutPaid(): bool
    {
        return $this->payout()->where('status', PayoutRequestStatus::APPROVED->value)->exists();
    }

    public function manuallyDeliveredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manually_delivered_by', 'id');
    }
}
