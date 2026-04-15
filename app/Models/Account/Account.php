<?php

namespace App\Models\Account;

use App\Enums\AccountTypes;
use App\Models\Merchant\MerchantTransaction;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasTimezone;
    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['account_type', 'account_type_id', 'account_type_name'];

    protected $casts = [
        'account_type' => 'integer',
    ];

    public function getAccountTypeIdAttribute(): int
    {
        return $this->attributes['account_type'];
    }

    public function getAccountTypeNameAttribute(): string
    {
        $accountTypeEnum = AccountTypes::tryFrom((int) $this->attributes['account_type']);

        return $accountTypeEnum instanceof AccountTypes ? $accountTypeEnum->getValues() : 'Unknown';
    }

    public function getAccountTypeAttribute(): ?array
    {
        $accountTypeEnum = AccountTypes::tryFrom((int) $this->attributes['account_type']);

        return $accountTypeEnum instanceof AccountTypes
            ? [
                'id'   => $accountTypeEnum->value,
                'name' => $accountTypeEnum->getValues(),
            ]
            : null;
    }

    public function merchantTransactions(): HasMany
    {
        return $this->hasMany(MerchantTransaction::class);
    }
}
