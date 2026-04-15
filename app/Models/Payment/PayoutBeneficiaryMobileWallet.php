<?php

namespace App\Models\Payment;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class PayoutBeneficiaryMobileWallet extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';


    public function payoutBeneficiaries()
    {
        return $this->hasMany(PayoutBeneficiary::class, 'payout_beneficiary_mobile_wallet_id');
    }

}
