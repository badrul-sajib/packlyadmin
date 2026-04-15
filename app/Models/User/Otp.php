<?php

namespace App\Models\User;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
