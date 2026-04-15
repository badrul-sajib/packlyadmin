<?php

namespace App\Broadcasting;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SocketChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    public function send($notifiable, Notification $notification)
    {
        // Get the notification data
        $messageData = $notification->toFcm($notifiable);

        // Prepare the notification array
        $notificationData = [
            'user_id'         => $messageData['user_id'],
            'title'           => $messageData['title'],
            'message'         => $messageData['body'],
            'action_url'      => $messageData['action_url'],
            'notification_id' => $messageData['data']['notification_id'],
            'created_at'      => now()->diffForHumans(),
        ];

        // Initialize Guzzle client
        $client = new Client;
        $appKey = config('app.key');

        try {

            $client->post(config('app.url').'/api/v1/merchant/notification-webhook', [
                'json'    => $notificationData,
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer '.$appKey,
                ],
            ]);

        } catch (RequestException $e) {
            // Log errors and return false
            Log::error('Failed to send notification: '.$e->getMessage());
        }
    }
}
