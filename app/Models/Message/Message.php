<?php

namespace App\Models\Message;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];
}
