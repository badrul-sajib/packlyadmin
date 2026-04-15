<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        // Return array of channels based on notification preferences
        return [
            // 'mail',
            'database',
            'broadcast',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->data['subject'])
            ->greeting('Hello '.$notifiable->name)
            ->line($this->data['message'])
            ->action($this->data['action_text'] ?? 'View Details', $this->data['action_url'] ?? url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title'      => $this->data['subject'],
            'message'    => $this->data['message'],
            'type'       => $this->data['type']             ?? 'info',
            'action_url' => $this->data['action_url']       ?? '',
            'created_at' => now(),
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'      => $this->data['subject'],
            'message'    => $this->data['message'],
            'type'       => $this->data['type']             ?? 'info',
            'action_url' => $this->data['action_url']       ?? '',
        ]);
    }
}
