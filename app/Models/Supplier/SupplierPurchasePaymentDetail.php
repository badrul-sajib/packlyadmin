<?php

namespace App\Models\Supplier;

use App\Models\Account\Account;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPurchasePaymentDetail extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function supplierPurchasePayment(): BelongsTo
    {
        return $this->belongsTo(SupplierPurchasePayment::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
