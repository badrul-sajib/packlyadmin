<?php

namespace App\Models\Payment;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayoutBeneficiaryBank extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];
}
