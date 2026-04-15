<?php

namespace App\Models\Giveaway;

use App\Models\Order\Order;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GiveawayTicket extends Model
{
    use HasFactory;

    protected $connection = 'mysql_internal';

    protected $guarded = ['id'];

    protected $casts = [
        'is_winner' => 'boolean',
    ];

    public function giveaway(): BelongsTo
    {
        return $this->belongsTo(Giveaway::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function draw(): HasOne
    {
        return $this->hasOne(GiveawayDraw::class, 'ticket_id');
    }
}
