<?php

namespace App\Models\Stock;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];
}
