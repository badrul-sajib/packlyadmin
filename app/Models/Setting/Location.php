<?php

namespace App\Models\Setting;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';
}
