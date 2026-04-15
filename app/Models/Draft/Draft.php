<?php

namespace App\Models\Draft;

use App\Models\Draft\DraftChange;
use Illuminate\Database\Eloquent\Model;

class Draft extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];


    public function changes()
    {
        return $this->hasMany(DraftChange::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}
