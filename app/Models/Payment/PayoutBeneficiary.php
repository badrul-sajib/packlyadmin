<?php

namespace App\Models\Payment;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayoutBeneficiary extends Model
{
    use HasTimezone;

    protected $guarded = [];

    protected $connection = 'mysql_internal';

    public function beneficiaryTypes(): BelongsTo
    {
        return $this->belongsTo(PayoutBeneficiaryTypes::class, 'payout_beneficiary_type_id');
    }

    public function beneficiaryType(): HasOne|Builder|PayoutBeneficiary
    {
        return $this->hasOne(PayoutBeneficiaryTypes::class, 'id', 'payout_beneficiary_type_id');
    }

    public function mobileWallet(): BelongsTo
    {
        return $this->belongsTo(PayoutBeneficiaryMobileWallet::class, 'payout_beneficiary_mobile_wallet_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(PayoutBeneficiaryBank::class, 'payout_beneficiary_bank_id');
    }

    public function getMobileWalletOrBankAttribute()
    {
        return $this->mobileWallet ?? $this->bank;
    }
}
