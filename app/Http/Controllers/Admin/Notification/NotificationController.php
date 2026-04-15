<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index()
    {
        // Get admin role notifications
        $notifications = auth()->user()->notifications()
            ->where('type', 'LIKE', '%Admin%')
            ->latest()
            ->paginate(10);

        $transformedNotifications = $notifications->map(function ($notification) {
            return [
                'id'         => $notification->id,
                'message'    => $notification->data['message'] ?? '',
                'read_at'    => $notification->read_at,
                'created_at' => $notification->created_at,
                'data'       => $notification->data,
                'type'       => $notification->type,
            ];
        });

        return response()->json([
            'notifications' => [
                'data'         => $transformedNotifications,
                'total'        => $notifications->total(),
                'per_page'     => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
            ],
            'unread_count' => auth()->user()->unreadNotifications()
                ->where('type', 'LIKE', '%Admin%')
                ->count(),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()
            ->where('type', 'LIKE', '%Admin%')
            ->get()
            ->each(function ($notification) {
                $notification->markAsRead();
            });

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    public function getCount()
    {
        return response()->json([
            'unread_count' => auth()->user()->unreadNotifications()
                ->where('type', 'LIKE', '%Admin%')
                ->count(),
        ]);
    }

    public function unread()
    {
        // Get only unread admin notifications
        $notifications = auth()->user()->unreadNotifications()
            ->where('type', 'LIKE', '%Admin%')
            ->latest()
            ->paginate(10);

        $transformedNotifications = $notifications->map(function ($notification) {
            return [
                'id'         => $notification->id,
                'message'    => $notification->data['message'] ?? '',
                'read_at'    => null, // Always null for unread notifications
                'created_at' => $notification->created_at,
                'data'       => $notification->data,
                'type'       => $notification->type,
                'category'   => $notification->data['category']     ?? 'general',
                'priority'   => $notification->data['priority']     ?? 'normal',
                'action_url' => $notification->data['action_url']   ?? null,
                'time_ago'   => Carbon::parse($notification->created_at)->diffForHumans(),
            ];
        });

        return response()->json([
            'notifications' => [
                'data'         => $transformedNotifications,
                'total'        => $notifications->total(),
                'per_page'     => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
            ],
            'unread_count' => auth()->user()->unreadNotifications()
                ->where('type', 'LIKE', '%Admin%')
                ->count(),
            'categories'          => $this->getNotificationCategories($notifications),
            'latest_notification' => $notifications->first() ? [
                'message' => $notifications->first()->data['message'] ?? '',
                'time'    => Carbon::parse($notifications->first()->created_at)->diffForHumans(),
            ] : null,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    private function getNotificationCategories($notifications)
    {
        // Get unique categories from notifications
        $categories = [];
        foreach ($notifications as $notification) {
            $category = $notification->data['category'] ?? 'general';
            if (! isset($categories[$category])) {
                $categories[$category] = 1;
            } else {
                $categories[$category]++;
            }
        }

        return $categories;
    }
}
