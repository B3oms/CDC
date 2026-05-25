<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Barangay;
use App\Models\Item;
use App\Models\Beneficiary;
use App\Models\LocationRequest;

class NotificationService
{
    /**
     * Create notification when a portal is opened
     */
    public static function portalOpened($portalId, $openedByUserId = null): void
    {
        $portal = Portal::find($portalId);
        if (!$portal) return;

        // Notify admin users
        $adminUsers = User::where('role_id', 1)->get();
        
        foreach ($adminUsers as $admin) {
            if ($openedByUserId && $admin->id === $openedByUserId) continue; // Don't notify the user who opened it
            
            Notification::createNotification(
                $admin->id,
                'portal_open',
                'Portal Opened',
                "Portal '{$portal->name}' has been opened",
                'portal',
                $portal->id
            );
        }
    }

    /**
     * Create notification when a report is submitted from barangay page
     */
    public static function barangayReportSubmitted($reportId, $barangayId): void
    {
        $barangay = Barangay::find($barangayId);
        if (!$barangay) return;

        // Notify admin and staff users
        $usersToNotify = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['Admin', 'Staff']);
        })->get();

        foreach ($usersToNotify as $user) {
            Notification::createNotification(
                $user->id,
                'recommendation_submitted',
                'New Recommendation Submitted',
                "A new beneficiary recommendation has been submitted from {$barangay->name}",
                'barangay',
                $barangay->id
            );
        }
    }

    /**
     * Create notification when inventory is added
     */
    public static function inventoryAdded($itemId, $addedByUserId): void
    {
        $item = Item::find($itemId);
        if (!$item) return;

        // Notify admin users
        $adminUsers = User::whereHas('role', function ($q) {
            $q->where('name', 'Admin');
        })->get();

        foreach ($adminUsers as $admin) {
            if ($admin->id === $addedByUserId) continue;

            Notification::createNotification(
                $admin->id,
                'inventory_addition',
                'New Item Added to Inventory',
                "New item '{$item->name}' has been added to inventory",
                'inventory',
                $item->id
            );
        }
    }

    /**
     * Create notification when a beneficiary is added
     */
    public static function beneficiaryAdded($beneficiaryId, $addedByUserId): void
    {
        $beneficiary = Beneficiary::find($beneficiaryId);
        if (!$beneficiary) return;

        $name = trim("{$beneficiary->first_name} {$beneficiary->last_name}");

        // Notify admin and staff users
        $usersToNotify = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['Admin', 'Staff']);
        })->get();

        foreach ($usersToNotify as $user) {
            if ($user->id === $addedByUserId) continue;

            Notification::createNotification(
                $user->id,
                'beneficiary_addition',
                'New Beneficiary Added',
                "New beneficiary '{$name}' has been added",
                'beneficiary',
                $beneficiary->id
            );
        }
    }

    /**
     * Create notification when an event is created
     */
    public static function eventCreated($eventId, $createdByUserId): void
    {
        $event = \App\Models\ReliefEvent::with('eventBarangays')->find($eventId);
        if (!$event) return;

        // Notify admin and staff users
        $usersToNotify = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['Admin', 'Staff']);
        })->get();

        foreach ($usersToNotify as $user) {
            if ($user->id === $createdByUserId) continue;

            Notification::createNotification(
                $user->id,
                'event_creation',
                'New Relief Event Created',
                "Relief event '{$event->name}' has been created",
                'event',
                $event->id
            );
        }

        // Notify barangay partner users whose barangay is included in this event
        $barangayIds = $event->eventBarangays->pluck('barangay_id')->filter()->unique();

        if ($barangayIds->isNotEmpty()) {
            $barangayUsers = User::whereHas('role', function ($q) {
                    $q->where('name', 'Barangay Partner');
                })
                ->whereIn('barangay_id', $barangayIds)
                ->get();

            foreach ($barangayUsers as $user) {
                Notification::createNotification(
                    $user->id,
                    'event_creation',
                    'Relief Event for Your Barangay',
                    "Your barangay has been included in relief event '{$event->name}'",
                    'event',
                    $event->id
                );
            }
        }
    }

    /**
     * Create notification when a location request is approved
     */
    public static function locationRequestApproved($requestId, $approvedByAdminId): void
    {
        $locationRequest = LocationRequest::find($requestId);
        if (!$locationRequest) return;

        // Notify the staff who submitted the request
        Notification::createNotification(
            $locationRequest->requested_by,
            'location_request_approved',
            'Location Request Approved',
            "Your {$locationRequest->type} request for '{$locationRequest->name}' has been approved",
            'location_request',
            $locationRequest->id
        );
    }

    /**
     * Create notification when a location request is rejected
     */
    public static function locationRequestRejected($requestId, $rejectedByAdminId, $rejectionReason = null): void
    {
        $locationRequest = LocationRequest::find($requestId);
        if (!$locationRequest) return;

        $message = "Your {$locationRequest->type} request for '{$locationRequest->name}' has been rejected";
        if ($rejectionReason) {
            $message .= ". Reason: {$rejectionReason}";
        }

        // Notify the staff who submitted the request
        Notification::createNotification(
            $locationRequest->requested_by,
            'location_request_rejected',
            'Location Request Rejected',
            $message,
            'location_request',
            $locationRequest->id
        );
    }

    /**
     * Create notification when a recommendation is converted to a beneficiary
     */
    public static function recommendationConverted($recommendedId): void
    {
        $recommended = \App\Models\RecommendedBeneficiary::find($recommendedId);
        if (!$recommended) return;

        $name = trim("{$recommended->first_name} {$recommended->last_name}");

        Notification::createNotification(
            $recommended->submitted_by,
            'recommendation_converted',
            'Recommendation Accepted',
            "Your recommended beneficiary '{$name}' has been accepted and verified.",
            'recommended_beneficiary',
            $recommended->id
        );
    }

    /**
     * Create notification when a recommendation is rejected
     */
    public static function recommendationRejected($recommendedId): void
    {
        $recommended = \App\Models\RecommendedBeneficiary::find($recommendedId);
        if (!$recommended) return;

        $name = trim("{$recommended->first_name} {$recommended->last_name}");

        Notification::createNotification(
            $recommended->submitted_by,
            'recommendation_rejected',
            'Recommendation Rejected',
            "Your recommended beneficiary '{$name}' has been rejected.",
            'recommended_beneficiary',
            $recommended->id
        );
    }

    /**
     * Create notification when a calamity event is opened for specific barangays
     */
    public static function calamityOpened($calamityId, $createdByUserId): void
    {
        $calamity = \App\Models\Calamity::with('barangays')->find($calamityId);
        if (!$calamity) return;

        $barangayIds = $calamity->barangays->pluck('id')->filter()->unique();
        if ($barangayIds->isEmpty()) return;

        $barangayUsers = User::whereHas('role', function ($q) {
                $q->where('name', 'Barangay Partner');
            })
            ->whereIn('barangay_id', $barangayIds)
            ->get();

        foreach ($barangayUsers as $user) {
            Notification::createNotification(
                $user->id,
                'calamity_opened',
                'Calamity Event in Your Barangay',
                "A calamity event '{$calamity->name}' has been opened for your barangay.",
                'calamity',
                $calamity->id
            );
        }
    }

    /**
     * Create notification when a calamity event is created
     */
    public static function calamityCreated($calamityId, $createdByUserId): void
    {
        $calamity = \App\Models\Calamity::find($calamityId);
        if (!$calamity) return;

        // Notify admin and other staff users
        $usersToNotify = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['Admin', 'Staff']);
            })
            ->where('id', '!=', $createdByUserId)
            ->get();

        foreach ($usersToNotify as $user) {
            Notification::createNotification(
                $user->id,
                'calamity_created',
                'New Calamity Event Created',
                "A new calamity event '{$calamity->name}' has been created.",
                'calamity',
                $calamity->id
            );
        }
    }

    /**
     * Create notification when inventory is updated
     */
    public static function inventoryUpdated($itemId, $updatedByUserId): void
    {
        $item = Item::find($itemId);
        if (!$item) return;

        // Notify admin and other staff users
        $usersToNotify = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['Admin', 'Staff']);
            })
            ->where('id', '!=', $updatedByUserId)
            ->get();

        foreach ($usersToNotify as $user) {
            Notification::createNotification(
                $user->id,
                'inventory_updated',
                'Inventory Updated',
                "Inventory item '{$item->name}' has been updated.",
                'inventory',
                $item->id
            );
        }
    }

    /**
     * Create notification when item is out of stock
     */
    public static function stockLow($itemId, $threshold = 10): void
    {
        $item = Item::find($itemId);
        if (!$item) return;

        // Notify admin and staff users
        $usersToNotify = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['Admin', 'Staff']);
            })
            ->get();

        foreach ($usersToNotify as $user) {
            Notification::createNotification(
                $user->id,
                'stock_low',
                'Item Out of Stock',
                "Item '{$item->name}' is out of stock or below threshold.",
                'inventory',
                $item->id
            );
        }
    }

    /**
     * Create notification when item is about to expire
     */
    public static function expirySoon($itemId, $daysLeft = 7): void
    {
        $item = Item::find($itemId);
        if (!$item) return;

        // Notify admin and staff users
        $usersToNotify = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['Admin', 'Staff']);
            })
            ->get();

        foreach ($usersToNotify as $user) {
            Notification::createNotification(
                $user->id,
                'expiry_soon',
                'Item Expiring Soon',
                "Item '{$item->name}' expires in {$daysLeft} days.",
                'inventory',
                $item->id
            );
        }
    }

    /**
     * Get unread notifications count for a user
     */
    public static function getUnreadCount($userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }

    /**
     * Get recent notifications for a user
     */
    public static function getRecentNotifications($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsRead($userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->update(['read' => true]);
    }
}
