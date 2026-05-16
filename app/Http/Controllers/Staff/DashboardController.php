<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\ReliefEvent;
use App\Models\Calamity;
use App\Models\Barangay;
use App\Models\Municipality;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $barangayCount     = Barangay::count();
        $municipalityCount = Municipality::count();
        $regionCount       = Municipality::distinct('province')->count('province');

        $staff = User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'Staff'))
            ->limit(3)->get();

        $upcomingEvents = ReliefEvent::with(['eventBarangays.barangay'])
            ->whereIn('status', ['Upcoming', 'Ongoing'])
            ->orderBy('date')
            ->get();

        $completedEvents = ReliefEvent::with(['eventBarangays.barangay'])
            ->where('status', 'Done')
            ->latest('date')
            ->get();

        $yearlyData = ReliefEvent::select(
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('date', '>=', now()->year - 2)
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get()
            ->groupBy('year');

        $monthlyData = ReliefEvent::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Prepare yearly trend data for charts
        $yearlyTrendLabels = $yearlyData->keys()->toArray();
        $yearlyTrendValues = $yearlyData->map(function($months) {
            return $months->sum('total');
        })->all();

        $activeCalamity = Calamity::where('status', 'Open')->latest()->first();

        return view('staff.dashboard', compact(
            'barangayCount', 'municipalityCount', 'regionCount',
            'staff', 'upcomingEvents', 'completedEvents',
            'yearlyData', 'monthlyData', 'yearlyTrendLabels', 'yearlyTrendValues', 'activeCalamity'
        ));
    }

    /**
     * Get staff notifications
     */
    public function getNotifications()
    {
        $notifications = NotificationService::getRecentNotifications(auth()->id(), 10);
        $unreadCount = NotificationService::getUnreadCount(auth()->id());

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                $url = '#';
            if ($notification->type === 'recommended_beneficiary') {
                $url = route('staff.recommended.index');
            } elseif ($notification->type === 'relief_operation_feedback') {
                $url = route('staff.relief.index');
            }

            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'text' => $notification->message,
                'type' => $notification->type,
                'icon' => $notification->icon,
                'color' => $notification->color,
                'read' => $notification->read,
                'unread' => !$notification->read,
                'time' => $notification->created_at->diffForHumans(),
                'related_type' => $notification->related_type,
                'related_id' => $notification->related_id,
                'url' => $url,
            ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($notificationId)
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
    public function markAllNotificationsRead()
    {
        $markedCount = NotificationService::markAllAsRead(auth()->id());

        return response()->json([
            'success' => true,
            'marked_count' => $markedCount,
            'unread_count' => 0,
        ]);
    }
}