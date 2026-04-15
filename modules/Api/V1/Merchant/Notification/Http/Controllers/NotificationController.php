<?php

namespace Modules\Api\V1\Merchant\Notification\Http\Controllers;

use App\Events\NotificationSent;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{

    public function __construct()
    {
        $this->middleware('shop.permission:show-notifications')->only('index', 'unread', 'getCount');
        $this->middleware('shop.permission:mark-notification-as-read')->only('markAsRead', 'markAllAsRead');
    }

    /*
     * Lists all notifications.
     */
    public function index(): JsonResponse
    {

        // Get Merchant role notifications
        $notifications = auth()->user()->merchant->notifications()
            ->where('type', 'LIKE', '%Merchant%')
            ->latest()
            ->paginate(10);

        $transformedNotifications = $notifications->map(function ($notification) {
            return [
                'id'         => $notification->id,
                'message'    => $notification->data['message'] ?? '',
                'read_at'    => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans(),
                'created_time' => $notification->created_at,
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
            'unread_count' => auth()->user()->merchant->unreadNotifications()
                ->where('type', 'LIKE', '%Merchant%')
                ->count(),
        ], Response::HTTP_OK, [], JSON_UNESCAPED_UNICODE);
    }

    /*
     * Marks one notification as read.
     */
    public function markAsRead($id): JsonResponse
    {
        $notification = auth()->user()->merchant->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /*
     * Marks all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->merchant->unreadNotifications()
            ->where('type', 'LIKE', '%Merchant%')
            ->get()
            ->each(function ($notification) {
                $notification->markAsRead();
            });

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /*
     * Returns count of unread notifications.
     */
    public function getCount(): JsonResponse
    {
        return response()->json([
            'unread_count' => auth()->user()->unreadNotifications()
                ->where('type', 'LIKE', '%Merchant%')
                ->count(),
        ]);
    }

    /*
     * Lists unread notifications.
     */
    public function unread(): JsonResponse
    {
        // Get only unread Merchant notifications
        $notifications = auth()->user()->unreadNotifications()
            ->where('type', 'LIKE', '%Merchant%')
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
                'category'   => $notification->data['category']   ?? 'general',
                'priority'   => $notification->data['priority']   ?? 'normal',
                'action_url' => $notification->data['action_url'] ?? null,
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
                ->where('type', 'LIKE', '%Merchant%')
                ->count(),
            'categories'          => $this->getNotificationCategories($notifications),
            'latest_notification' => $notifications->first() ? [
                'message' => $notifications->first()->data['message'] ?? '',
                'time'    => Carbon::parse($notifications->first()->created_at)->diffForHumans(),
            ] : null,
        ], Response::HTTP_OK, [], JSON_UNESCAPED_UNICODE);
    }

    private function getNotificationCategories($notifications): array
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

    /*
     * Sends a notification via webhook.
     */
    public function webhook(Request $request)
    {

        $appKey = config('app.key');

        // authorize bearer token
        if ($request->bearerToken() !== $appKey) {
            return response()->json([
                'error' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return broadcast(new NotificationSent([
            'user_id'         => $request->user_id,
            'title'           => $request->title,
            'message'         => $request->message,
            'action_url'      => $request->action_url,
            'notification_id' => $request->notification_id,
            'created_at'      => now()->diffForHumans(),
        ]));
    }
}
