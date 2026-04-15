<?php

namespace App\Models\Account;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class JournalDetail extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];
}
