<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = NotificationService::getRecentNotifications(auth()->id(), 10);
        $unreadCount   = NotificationService::getUnreadCount(auth()->id());

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
                    'url'   => $this->resolveUrl($notification->type),
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    private function resolveUrl(string $type): string
    {
        try {
            return match($type) {
                'recommendation_submitted',
                'recommendation_converted',
                'recommendation_rejected'   => route('barangay.recommendations.index'),
                'event_creation'            => route('barangay.dashboard'),
                default                     => '#',
            };
        } catch (\Exception $e) {
            return '#';
        }
    }

    public function markAsRead($notificationId): JsonResponse
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($notificationId);

        $notification->markAsRead();

        return response()->json([
            'success'      => true,
            'unread_count' => NotificationService::getUnreadCount(auth()->id()),
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        NotificationService::markAllAsRead(auth()->id());

        return response()->json(['success' => true, 'unread_count' => 0]);
    }
}
