<?php

namespace App\Models\Chat;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class ConversationUser extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';
}
