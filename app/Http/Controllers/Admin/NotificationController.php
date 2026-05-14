<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Get notifications for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = NotificationService::getRecentNotifications(auth()->id(), 10);
        $unreadCount = NotificationService::getUnreadCount(auth()->id());

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'read' => $notification->read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'related_type' => $notification->related_type,
                    'related_id' => $notification->related_id,
                    'url' => '#', // Default URL for now
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($notificationId): JsonResponse
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($notificationId);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => NotificationService::getUnreadCount(auth()->id()),
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $markedCount = NotificationService::markAllAsRead(auth()->id());

        return response()->json([
            'success' => true,
            'marked_count' => $markedCount,
            'unread_count' => 0,
        ]);
    }
}
