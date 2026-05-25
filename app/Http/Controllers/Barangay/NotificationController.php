<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    private const BARANGAY_TYPES = [
        'recommendation_converted',
        'recommendation_rejected',
        'event_creation',
        'calamity_opened',
    ];

    public function index(Request $request): JsonResponse
    {
        $notifications = \App\Models\Notification::where('user_id', auth()->id())
            ->whereIn('type', self::BARANGAY_TYPES)
            ->latest()
            ->limit(10)
            ->get();

        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
            ->whereIn('type', self::BARANGAY_TYPES)
            ->where('read', false)
            ->count();

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
                'event_creation'            => route('barangay.relief-events.index'),
                'calamity_opened'           => route('barangay.dashboard'),
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

        $unreadCount = Notification::where('user_id', auth()->id())
            ->whereIn('type', self::BARANGAY_TYPES)
            ->where('read', false)
            ->count();

        return response()->json([
            'success'      => true,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->whereIn('type', self::BARANGAY_TYPES)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['success' => true, 'unread_count' => 0]);
    }
}
