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
                    'id'    => $notification->id,
                    'title' => $notification->title,
                    'text'  => $notification->message,
                    'type'  => $notification->type,
                    'icon'  => $notification->icon,
                    'color' => $notification->color,
                    'unread'=> !$notification->read,
                    'time'  => $notification->created_at->diffForHumans(),
                    'url'   => self::resolveUrl($notification->type, $notification->related_type, $notification->related_id),
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    private static function resolveUrl(string $type, ?string $relatedType, ?int $relatedId): string
    {
        try {
            return match($type) {
                'beneficiary_addition'      => route('admin.beneficiaries.index'),
                'inventory_addition'        => route('admin.inventory.index'),
                'event_creation'            => route('admin.relief.index'),
                'recommendation_submitted'  => route('admin.recommended.index'),
                'location_request_approved',
                'location_request_rejected' => route('admin.location-requests.index'),
                default                     => '#',
            };
        } catch (\Exception $e) {
            return '#';
        }
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
