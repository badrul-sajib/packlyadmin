<?php

namespace App\Models\Shop;

use App\Models\Merchant\Merchant;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopUpdateRequest extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    public $casts = [
        'created_at' => 'datetime:Y-m-d H:i:A',
        'updated_at' => 'datetime:Y-m-d H:i:A',
    ];

    protected $guarded = [];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
