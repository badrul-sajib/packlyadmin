<?php

namespace App\Models\User;

use App\Traits\HasTimezone;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasTimezone;

    protected $connection = 'mysql_external';
}
