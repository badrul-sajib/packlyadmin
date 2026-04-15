<?php

namespace App\Jobs;

use App\Events\NotificationSent;
use App\Models\Notification\Notification;
use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class PushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $admins = User::isAdmin()->get();

            foreach ($admins as $admin) {

                $notification_id = Str::uuid()->toString();

                Notification::create([
                    'id'              => $notification_id,
                    'type'            => 'App\\Notifications\\AdminNotification',
                    'notifiable_type' => 'App\\Models\\User\\User',
                    'notifiable_id'   => $admin->id,
                    'data'            => [
                        'title'              => $this->data['title'],
                        'message'            => $this->data['message'],
                        'type'               => $this->data['type']       ?? 'info',
                        'action_url'         => $this->data['action_url'] ?? null,
                        'admin_notification' => true,
                    ],
                    'read_at' => null,
                ]);

                broadcast(new NotificationSent([
                    'user_id'         => $admin->id,
                    'title'           => $this->data['title'],
                    'message'         => $this->data['message'],
                    'action_url'      => $this->data['action_url'],
                    'notification_id' => $notification_id,
                    'created_at'      => now()->diffForHumans(),
                ]));
            }
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
        }
    }
}
