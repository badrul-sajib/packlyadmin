<?php

namespace App\Models\Page;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    public $timestamps = false;

    protected $fillable = ['name', 'slug'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
