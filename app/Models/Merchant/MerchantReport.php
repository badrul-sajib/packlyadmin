<?php

namespace App\Models\Merchant;

use App\Models\User\User;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantReport extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $fillable = [
        'merchant_id',
        'report_details',
        'status',
        'added_by',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function getStatusAttribute($value): string
    {
        return ucfirst($value);
    }
}
