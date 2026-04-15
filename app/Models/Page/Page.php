<?php

namespace App\Models\Page;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
    ];
}
