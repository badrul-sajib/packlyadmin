<?php

namespace App\Models\Payout;

use App\Enums\PayoutRequestStatus;
use App\Models\Merchant\Merchant;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payout extends Model
{
    use HasTimezone, LogsActivity;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = [
        'status_label',
    ];

    protected static array $logAttributes = ['status', 'amount'];

    protected static array $logAttributesToIgnore = [];

    protected static bool $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'amount']);
    }

    public function payoutBeneficiary(): BelongsTo
    {
        return $this->belongsTo(PayoutBeneficiary::class, 'payout_beneficiary_id');
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

    public array $cast = [
        'status' => PayoutRequestStatus::class,
    ];
}
