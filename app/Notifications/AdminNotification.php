<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private array $data;

    private string $type;

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
    public function __construct(string $type, array $data)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message'            => $this->data['message'],
            'type'               => $this->type,
            'action_url'         => $this->data['action_url'] ?? null,
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
