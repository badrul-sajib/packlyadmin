<?php

namespace App\Models\Draft;

use App\Models\Draft\Draft as DraftModel;
use Illuminate\Database\Eloquent\Model;

class DraftChange extends Model
{
    protected $fillable = [
        'draft_id',
        'field',
        'old_value',
        'new_value',
        'cast_type',
    ];

    public function draft()
    {
        return $this->belongsTo(DraftModel::class);
    }
}

