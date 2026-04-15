<?php

namespace App\Models\Wallet;

use App\Models\Merchant\Merchant;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
