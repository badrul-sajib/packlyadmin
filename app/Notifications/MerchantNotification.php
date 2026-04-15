<?php

namespace App\Notifications;

use App\Broadcasting\SocketChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class MerchantNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private array $data;

    private string $type;

    private int $userId;

    // private $fcmTokens = [];

    public static string $INFO = 'info';

    public static string $WARNING = 'warning';

    public static string $ERROR = 'error';

    public static string $SUCCESS = 'success';

    /**
     *  Create a new notification instance.
     *
     * @param  string  $type  self::$INFO | self::$WARNING | self::$ERROR | self::$SUCCESS
     * @return void
     */
    public function __construct(string $type, array $data, int $userId)
    {
        $this->data      = $data;
        $this->type      = $type;
        $this->userId    = $userId;
    }

    public function via($notifiable): array
    {
        return [SocketChannel::class, 'database'];
    }

    public function toFcm($notifiable): array
    {
        return [
            'user_id'    => $this->userId,
            'title'      => $this->data['title'],
            'body'       => $this->data['message'],
            'action_url' => $this->data['action_url'] ?? null,
            'data'       => [
                'notification_id' => $this->id,
                // 'fcm_tokens'      => $this->fcmTokens,
            ],
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'message'            => $this->data['message'],
            'type'               => $this->type,
            'action_url'         => $this->data['action_url'] ?? null,
            'admin_notification' => true,
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message'            => $this->data['message'],
            'type'               => $this->data['type']             ?? 'info',
            'action_url'         => $this->data['action_url']       ?? null,
        ]);
    }
}
