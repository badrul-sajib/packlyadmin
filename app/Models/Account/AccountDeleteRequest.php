<?php

namespace App\Models\Account;

use App\Models\User\User;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountDeleteRequest extends Model
{
    use HasTimezone;
    protected $guarded = [];

    const STATUS_PENDING = 1;

    const STATUS_APPROVED = 2;

    const STATUS_REJECTED = 3;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
