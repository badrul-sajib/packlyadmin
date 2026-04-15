<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    public function __construct($data)
    {
        $this->data = $data;
        Log::info('Dispatching Notification Sent', $this->data);
    }

    public function broadcastOn(): Channel
    {
        return new Channel('notifications_'.$this->data['user_id']);
    }

}
