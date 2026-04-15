<?php

namespace App\Models\Account;

use App\Models\Stock\Transfer;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BeneficiaryAccount extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function transfersTo(): Builder|HasMany|BeneficiaryAccount
    {
        return $this->hasMany(Transfer::class, 'to_account_id');
    }

    public function transfersFrom(): Builder|HasMany|BeneficiaryAccount
    {
        return $this->hasMany(Transfer::class, 'from_account_id');
    }
}
