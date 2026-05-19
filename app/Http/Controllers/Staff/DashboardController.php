<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReliefEvent;
use App\Models\Calamity;
use App\Models\Barangay;
use App\Models\Municipality;
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
        $notifications = [];
        $unreadCount = 0;

        try {
            // Get upcoming relief events (next 7 days) - most reliable data
            $upcomingEvents = ReliefEvent::where('status', 'Upcoming')
                ->where('date', '<=', now()->addDays(7))
                ->where('date', '>=', now())
                ->count();

            if ($upcomingEvents > 0) {
                $notifications[] = [
                    'id' => 'relief-events-' . now()->timestamp,
                    'title' => 'Upcoming relief events',
                    'text' => "{$upcomingEvents} relief event(s) scheduled for next 7 days",
                    'time' => 'Scheduled',
                    'unread' => false,
                    'icon' => 'fas fa-hands-helping',
                    'color' => '#3b82f6',
                    'url' => route('staff.relief.index')
                ];
            }

            // Get recent relief events (last 24 hours) - fallback notification
            $recentEvents = ReliefEvent::where('created_at', '>=', now()->subDay())
                ->count();

            if ($recentEvents > 0) {
                $notifications[] = [
                    'id' => 'recent-events-' . now()->timestamp,
                    'title' => 'Recent relief activity',
                    'text' => "{$recentEvents} relief event(s) created in the last 24 hours",
                    'time' => 'Today',
                    'unread' => true,
                    'icon' => 'fas fa-calendar',
                    'color' => '#10b981',
                    'url' => route('staff.relief.index')
                ];
                $unreadCount++;
            }

            // Get total relief events as general info
            $totalEvents = ReliefEvent::count();
            if ($totalEvents > 0) {
                $notifications[] = [
                    'id' => 'total-events-' . now()->timestamp,
                    'title' => 'Total relief operations',
                    'text' => "Currently tracking {$totalEvents} relief event(s) in the system",
                    'time' => 'System',
                    'unread' => false,
                    'icon' => 'fas fa-chart-line',
                    'color' => '#6b7280',
                    'url' => route('staff.relief.index')
                ];
            }

            // Add a welcome notification if no others exist
            if (empty($notifications)) {
                $notifications[] = [
                    'id' => 'welcome-' . now()->timestamp,
                    'title' => 'Welcome to Staff Dashboard',
                    'text' => 'You can create relief events and manage operations from here',
                    'time' => 'Info',
                    'unread' => false,
                    'icon' => 'fas fa-info-circle',
                    'color' => '#3b82f6',
                    'url' => route('staff.relief.create')
                ];
            }

        } catch (\Exception $e) {
            // Fallback notification if database queries fail
            $notifications[] = [
                'id' => 'system-' . now()->timestamp,
                'title' => 'Staff Dashboard',
                'text' => 'Manage relief operations and track events',
                'time' => 'System',
                'unread' => false,
                'icon' => 'fas fa-hands-helping',
                'color' => '#1a3d1f',
                'url' => route('staff.relief.index')
            ];
        }

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($notificationId)
    {
        // For now, just return success since we don't have a notifications table
        // In a real implementation, you would update a notifications table
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        // For now, just return success since we don't have a notifications table
        // In a real implementation, you would update all notifications for the user
        return response()->json(['success' => true]);
    }
}