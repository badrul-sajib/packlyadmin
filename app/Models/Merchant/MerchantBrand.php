<?php

namespace App\Models\Merchant;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class MerchantBrand extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];
}
