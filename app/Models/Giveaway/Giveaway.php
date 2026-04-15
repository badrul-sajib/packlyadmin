<?php

namespace App\Models\Giveaway;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Giveaway extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $connection = 'mysql_internal';

    protected $guarded = ['id'];

    protected $casts = [
        'start_at' => 'datetime:Y-m-d H:i:s',
        'end_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->logOnly(['name','description','start_at', 'end_at','status'])
            ->dontSubmitEmptyLogs();
    }



    public function gifts(): HasMany
    {
        return $this->hasMany(GiveawayGift::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(GiveawayTicket::class);
    }

    public function draws(): HasMany
    {
        return $this->hasMany(GiveawayDraw::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('start_at', '<=', now())
                     ->where('end_at', '>=', now());
    }
}
