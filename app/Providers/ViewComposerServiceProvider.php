<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\ReliefEvent;
use App\Models\Beneficiary;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\User;
use App\Models\LocationRequest;
use App\Models\Calamity;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Share notification data with all views that use admin layout
        View::composer('admin.layouts.app', function ($view) {
            $user = Auth::user();
            
            if (!$user) {
                return;
            }

            $notifications = collect();
            $unreadCount = 0;

            if ($user->role && $user->role->name === 'Barangay Partner') {
                // Barangay users only see notifications for their barangay
                $barangayId = $user->barangay_id;
                
                if ($barangayId) {
                    // Get relief operations for their barangay
                    $reliefNotifications = ReliefEvent::whereHas('eventBarangays', function ($query) use ($barangayId) {
                        $query->where('barangay_id', $barangayId);
                    })
                    ->where('created_at', '>=', now()->subDays(7))
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function ($relief) {
                        return (object) [
                            'id' => $relief->id,
                            'type' => 'relief_operation',
                            'title' => 'Relief Operation Scheduled',
                            'text' => "Relief operation '{$relief->name}' scheduled for your barangay",
                            'time' => $relief->created_at->diffForHumans(),
                            'icon' => 'fas fa-hands-helping',
                            'color' => '#185fa5',
                            'unread' => true,
                            'url' => route('barangay.dashboard')
                        ];
                    });

                    // Get portal/application openings for their barangay
                    $portalNotifications = \App\Models\Calamity::where('status', 'active')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->latest()
                    ->take(3)
                    ->get()
                    ->map(function ($calamity) {
                        return (object) [
                            'id' => $calamity->id,
                            'type' => 'portal_opening',
                            'title' => 'Portal/Application Opened',
                            'text' => "New portal opened for '{$calamity->name}' assistance",
                            'time' => $calamity->created_at->diffForHumans(),
                            'icon' => 'fas fa-external-link-alt',
                            'color' => '#ef9f27',
                            'unread' => true,
                            'url' => route('barangay.dashboard')
                        ];
                    });

                    // Add location request status notifications for staff
                    $locationRequestNotifications = LocationRequest::where('requested_by', $user->id)
                        ->whereIn('status', ['approved', 'rejected'])
                        ->where('updated_at', '>=', now()->subDays(7))
                        ->latest()
                        ->take(3)
                        ->get()
                        ->map(function ($request) {
                            $isApproved = $request->status === 'approved';
                            return (object) [
                                'id' => $request->id,
                                'type' => 'location_request',
                                'title' => $isApproved ? 'Location Request Approved' : 'Location Request Rejected',
                                'text' => "Your location request for '{$request->name}' has been {$request->status}",
                                'time' => $request->updated_at->diffForHumans(),
                                'icon' => $isApproved ? 'fas fa-check-circle' : 'fas fa-times-circle',
                                'color' => $isApproved ? '#3b6d11' : '#e24b4a',
                                'unread' => true,
                                'url' => route('staff.location-requests.index')
                            ];
                        });

                    $notifications = $reliefNotifications->merge($portalNotifications)->merge($locationRequestNotifications);
                    $unreadCount = $notifications->where('unread', true)->count();
                }
            } else {
                // Admin and Staff see all notifications
                $notifications = collect([
                    (object) [
                        'id' => 1,
                        'type' => 'calamity',
                        'title' => 'New calamity alert',
                        'text' => 'Typhoon warning issued for Cagayan',
                        'time' => '2 hours ago',
                        'icon' => 'fas fa-exclamation-triangle',
                        'color' => '#ef9f27',
                        'unread' => true,
                        'url' => route('admin.dashboard')
                    ],
                    (object) [
                        'id' => 2,
                        'type' => 'beneficiaries',
                        'title' => 'New beneficiaries registered',
                        'text' => '15 new beneficiaries awaiting approval',
                        'time' => '5 hours ago',
                        'icon' => 'fas fa-users',
                        'color' => '#185fa5',
                        'unread' => true,
                        'url' => route('admin.beneficiaries.index')
                    ],
                    (object) [
                        'id' => 3,
                        'type' => 'location_requests',
                        'title' => 'Location requests',
                        'text' => '3 new municipalities pending approval',
                        'time' => '1 day ago',
                        'icon' => 'fas fa-map-marker-alt',
                        'color' => '#3b6d11',
                        'unread' => false,
                        'url' => route('admin.location-requests.index')
                    ]
                ]);
                
                // Add pending location requests notifications for admin
                $pendingLocationRequests = LocationRequest::pending()->count();
                if ($pendingLocationRequests > 0) {
                    $notifications->push((object) [
                        'id' => 4,
                        'type' => 'location_requests',
                        'title' => 'New location requests',
                        'text' => "{$pendingLocationRequests} location request(s) pending approval",
                        'time' => 'Just now',
                        'icon' => 'fas fa-clipboard-list',
                        'color' => '#ef9f27',
                        'unread' => true,
                        'url' => route('admin.location-requests.index')
                    ]);
                }
                
                $unreadCount = $notifications->where('unread', true)->count();
            }

            $view->with([
                'userNotifications' => $notifications,
                'unreadNotificationCount' => $unreadCount,
                'canCreateItems' => $user->role && $user->role->name !== 'Barangay Partner'
            ]);
        });
    }

    public function register()
    {
        //
    }
}
