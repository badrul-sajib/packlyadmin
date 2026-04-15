<?php

namespace App\Models\Voucher;

use App\Enums\CommonType;
use App\Models\Merchant\Merchant;
use App\Models\User\User;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function merchants(): BelongsToMany
    {
        return $this->belongsToMany(Merchant::class, 'voucher_merchants');
    }

    public function voucherUsages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class, 'voucher_id', 'id');
    }

    public function getTypeLabel($filed): string
    {
        return CommonType::label()[$this->$filed->value];
    }

    protected $casts = [
        'merchant_type' => CommonType::class,
    ];
}
