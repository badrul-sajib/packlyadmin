<?php

namespace App\Models\Account;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function details(): Builder|HasMany|Journal
    {
        return $this->hasMany(JournalDetail::class);
    }
}
