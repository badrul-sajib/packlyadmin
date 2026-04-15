<?php

namespace App\Models\User;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasTimezone;

    protected $guarded = [];

    protected $connection = 'mysql_internal';
}
