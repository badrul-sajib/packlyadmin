<?php

namespace App\Models\Page;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasTimezone;

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
    ];
}
