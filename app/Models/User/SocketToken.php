<?php

namespace App\Models\User;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class SocketToken extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public array $cast = [
        'id'      => 'integer',
        'user_id' => 'integer',
    ];
}
