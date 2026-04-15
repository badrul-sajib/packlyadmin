<?php

namespace App\Models\Sell;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class SellWithUsPage extends Model
{
    use HasTimezone;

    protected $fillable = [
        'section_slug',
        'title',
        'subtitle',
        'data',
        'items',
        'item_structure',
    ];

    protected $casts = [
        'data'           => 'array',
        'items'          => 'array',
        'item_structure' => 'array',
    ];
}
