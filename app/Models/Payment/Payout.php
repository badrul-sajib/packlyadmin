<?php

namespace App\Models\Payment;

use App\Enums\PayoutRequestStatus;
use App\Models\Merchant\MerchantOrder;
use App\Models\User\User;
use App\Traits\HasTimezone;
use App\Models\Merchant\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payout extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'ready_at' => 'datetime',
        'held_at' => 'datetime',
    ];

    public function payoutBeneficiary(): BelongsTo
    {
        return $this->belongsTo(PayoutBeneficiary::class, 'payout_beneficiary_id', 'id');
    }

    public function merchantOrders(): HasMany
    {
        return $this->hasMany(MerchantOrder::class, 'payout_id');
    }

    public function payoutMerchantOrders(): BelongsToMany
    {
        return $this->belongsToMany(MerchantOrder::class, 'merchant_order_payout');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function getStatusLabelAttribute(): array
    {
        return [
            'value' => PayoutRequestStatus::getLabel()[$this->status],
            'bg_color' => PayoutRequestStatus::getBgColor()[$this->status],
        ];
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function readyBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ready_by');
    }

    public function heldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'held_by');
    }

    public function payoutMethodChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payout_method_changed_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
