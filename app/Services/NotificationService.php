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

        // Notify admin users
        $adminUsers = User::whereHas('role', function ($q) {
            $q->where('name', 'Admin');
        })->get();

        foreach ($adminUsers as $admin) {
            if ($admin->id === $addedByUserId) continue;

            Notification::createNotification(
                $admin->id,
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
        $event = \App\Models\ReliefEvent::find($eventId);
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
