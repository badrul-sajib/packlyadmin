<?php

namespace App\Models\Merchant;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MerchantFollower extends Pivot
{
    use HasTimezone;

    protected $table;

    protected $connection = 'mysql_internal';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $database = config('database.connections.mysql_internal.database');

        $this->table = $database.'.'.'merchant_follower';
    }
}
