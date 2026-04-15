<?php

namespace App\Models;

use App\Models\Order\Order;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use App\Enums\SpamOrderStatus;

class SpamAttempt extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'ip_address',
        'device_id',
        'fraud_score',
        'triggered_rules',
        'action_taken',
        'status',
        'metadata',
    ];

    public $casts = [
        'action_taken' => SpamOrderStatus::class,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
