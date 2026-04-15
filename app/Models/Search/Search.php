<?php

namespace App\Models\Search;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];
}
