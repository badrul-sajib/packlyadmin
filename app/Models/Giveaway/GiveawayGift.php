<?php

namespace App\Models\Giveaway;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GiveawayGift extends Model
{
    use HasFactory;

    protected $connection = 'mysql_internal';

    protected $guarded = ['id'];

    public function giveaway(): BelongsTo
    {
        return $this->belongsTo(Giveaway::class);
    }

    public function draw(): HasOne
    {
        return $this->hasOne(GiveawayDraw::class, 'gift_id');
    }
}
