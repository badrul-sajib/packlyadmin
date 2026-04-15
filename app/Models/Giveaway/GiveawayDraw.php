<?php

namespace App\Models\Giveaway;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GiveawayDraw extends Model
{
    use HasFactory;

    protected $connection = 'mysql_internal';

    protected $guarded = ['id'];

    protected $casts = [
        'drawn_at' => 'datetime',
    ];

    public function giveaway(): BelongsTo
    {
        return $this->belongsTo(Giveaway::class);
    }

    public function gift(): BelongsTo
    {
        return $this->belongsTo(GiveawayGift::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(GiveawayTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
