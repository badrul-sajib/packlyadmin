<?php

namespace App\Models\User;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class SearchUser extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];
}
