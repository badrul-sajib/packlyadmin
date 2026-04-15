<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    //
    protected $table = 'visitors';
    protected $fillable = [
        'ip_address',
        'url',
        'visit_count',
        'last_visit_at',
        'is_blocked',
    ];
    protected $casts = [
        'last_visit_at' => 'datetime',
        'is_blocked'    => 'bool',
    ];

    public function getIsActiveAttribute()
    {
        return $this->last_visit_at && $this->last_visit_at >= now()->subMinutes(5);
    }

    public function getStatusAttribute()
    {
        return $this->is_active ? 'active' : 'inactive';
    }
}
