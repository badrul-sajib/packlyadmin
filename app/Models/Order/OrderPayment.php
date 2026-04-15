<?php

namespace App\Models\Order;

use App\Enums\PaymentStatus;
use App\Models\Merchant\Merchant;
use App\Models\Merchant\MerchantOrder;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPayment extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['status_label', 'status_bg_color'];

    public static string $PENDING = '1';

    public static string $PAID = '2';

    public function order(): BelongsTo
    {
        return $this->belongsTo(MerchantOrder::class, 'merchant_order_id', 'id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function getStatusLabelAttribute(): string
    {
        return PaymentStatus::getStatusLabels()[$this->payment_status] ?? 'Pending';
    }

    public function getStatusBgColorAttribute()
    {
        $statuses = PaymentStatus::status_by_color() ?? [];

        return $statuses[$this->status_id] ?? 'alert-warning';
    }
}
