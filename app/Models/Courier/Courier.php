<?php

namespace App\Models\Courier;

use App\Models\Merchant\Merchant;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Courier extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public static int $STEADFAST_COURIER = 1;

    public static int $PATHAO_COURIER = 2;

    public static int $REDX_COURIER = 3;

    public static int $ECOURIER = 4;

    public static int $SUNDARBAN_COURIER = 5;

    public function merchants(): BelongsToMany
    {
        return $this->belongsToMany(Merchant::class, 'courier_merchant')
            ->withPivot('base_url', 'api_key', 'secret_key', 'is_default', 'is_active')
            ->withTimestamps();
    }
}
