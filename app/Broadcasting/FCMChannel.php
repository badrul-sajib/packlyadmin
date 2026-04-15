<?php

namespace App\Broadcasting;

use App\Services\Notification\Firebase;
use Illuminate\Notifications\Notification;

class FCMChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function send($notifiable, Notification $notification)
    {
        $messageData = $notification->toFcm($notifiable);
        (new Firebase)->send($messageData['title'], $messageData['body'], $messageData['data']['fcm_tokens'], [
            'action_url'      => $messageData['action_url'],
            'notification_id' => $messageData['data']['notification_id'],
            'created_at'      => now()->diffForHumans(),
        ]);
    }
}
