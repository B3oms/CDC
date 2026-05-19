<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Portal;
use App\Models\Barangay;
use App\Models\Inventory;
use App\Models\Beneficiary;
use App\Models\Event;
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

        // Notify admin users
        $adminUsers = User::where('role_id', 1)->get();
        
        foreach ($adminUsers as $admin) {
            Notification::createNotification(
                $admin->id,
                'barangay_report',
                'New Report Submitted',
                "A new report has been submitted from {$barangay->name}",
                'barangay',
                $barangay->id
            );
        }
    }

    /**
     * Create notification when inventory is added
     */
    public static function inventoryAdded($inventoryId, $addedByUserId): void
    {
        $inventory = Inventory::find($inventoryId);
        if (!$inventory) return;

        // Notify admin users
        $adminUsers = User::where('role_id', 1)->get();
        
        foreach ($adminUsers as $admin) {
            if ($admin->id === $addedByUserId) continue; // Don't notify the user who added it
            
            Notification::createNotification(
                $admin->id,
                'inventory_addition',
                'New Inventory Added',
                "New inventory '{$inventory->name}' has been added",
                'inventory',
                $inventory->id
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

        // Notify admin users
        $adminUsers = User::where('role_id', 1)->get();
        
        foreach ($adminUsers as $admin) {
            if ($admin->id === $addedByUserId) continue; // Don't notify the user who added it
            
            Notification::createNotification(
                $admin->id,
                'beneficiary_addition',
                'New Beneficiary Added',
                "New beneficiary '{$beneficiary->name}' has been added",
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

        // Notify admin users
        $adminUsers = User::where('role_id', 1)->get();
        
        foreach ($adminUsers as $admin) {
            if ($admin->id === $createdByUserId) continue; // Don't notify the user who created it
            
            Notification::createNotification(
                $admin->id,
                'event_creation',
                'New Event Created',
                "New event '{$event->name}' has been created",
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
