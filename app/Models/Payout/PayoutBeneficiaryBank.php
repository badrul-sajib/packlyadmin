<?php

namespace App\Models\Payout;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class PayoutBeneficiaryBank extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];
}
