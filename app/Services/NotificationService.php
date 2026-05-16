<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Portal;
use App\Models\Barangay;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Beneficiary;
use App\Models\Event;
use App\Models\LocationRequest;
use App\Models\RecommendedBeneficiary;
use App\Models\ReliefOperationFeedback;

class NotificationService
{
    protected static function getAdminUsers()
    {
        $users = User::whereHas('role', fn($q) => $q->where('name', 'Admin'))->get();
        if ($users->isEmpty()) {
            return User::where('role_id', 1)->get();
        }
        return $users;
    }

    protected static function getStaffUsers()
    {
        $staffRoleNames = ['Staff', 'Barangay Partner', 'Volunteer'];
        $users = User::whereHas('role', fn($q) => $q->whereIn('name', $staffRoleNames))->get();
        if ($users->isEmpty()) {
            return User::where('role_id', 2)->get();
        }
        return $users;
    }

    /**
     * Create notification when a portal is opened
     */
    public static function portalOpened($portalId, $openedByUserId = null): void
    {
        $portal = Portal::find($portalId);
        if (!$portal) return;

        // Notify admin users
        $adminUsers = self::getAdminUsers();
        
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
        $adminUsers = self::getAdminUsers();
        
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
     * Create notification when a barangay partner recommendation is submitted
     */
    public static function barangayRecommendationSubmitted($recommendationId, $submittedByUserId): void
    {
        $recommendation = RecommendedBeneficiary::with('barangay')->find($recommendationId);
        if (!$recommendation) return;

        $barangayName = $recommendation->barangay?->name ?? 'Unknown Barangay';
        $fullName = trim("{$recommendation->first_name} {$recommendation->middle_name} {$recommendation->last_name}");
        $contact = $recommendation->contact_number ?: 'N/A';
        $address = $recommendation->address ?: 'N/A';

        $gender = $recommendation->gender ? " Gender: {$recommendation->gender}." : '';
        $age = $recommendation->age ? " Age: {$recommendation->age}." : '';
        $notes = $recommendation->notes ? " Notes: {$recommendation->notes}." : '';
        $message = "New beneficiary recommendation from {$barangayName}: {$fullName}.{$gender}{$age} Contact: {$contact}. Address: {$address}.{$notes}";

        $staffUsers = self::getStaffUsers();
        foreach ($staffUsers as $staff) {
            if ($staff->id === $submittedByUserId) continue;
            Notification::createNotification(
                $staff->id,
                'recommended_beneficiary',
                'Barangay Recommendation Submitted',
                $message,
                'recommended_beneficiary',
                $recommendation->id
            );
        }
    }

    /**
     * Create notification when a barangay partner recommendation is updated
     */
    public static function barangayRecommendationUpdated($recommendationId, $updatedByUserId): void
    {
        $recommendation = RecommendedBeneficiary::with('barangay')->find($recommendationId);
        if (!$recommendation) return;

        $barangayName = $recommendation->barangay?->name ?? 'Unknown Barangay';
        $fullName = trim("{$recommendation->first_name} {$recommendation->middle_name} {$recommendation->last_name}");
        $contact = $recommendation->contact_number ?: 'N/A';
        $address = $recommendation->address ?: 'N/A';

        $gender = $recommendation->gender ? " Gender: {$recommendation->gender}." : '';
        $age = $recommendation->age ? " Age: {$recommendation->age}." : '';
        $notes = $recommendation->notes ? " Notes: {$recommendation->notes}." : '';
        $message = "Updated beneficiary recommendation from {$barangayName}: {$fullName}.{$gender}{$age} Contact: {$contact}. Address: {$address}.{$notes}";

        $staffUsers = self::getStaffUsers();
        foreach ($staffUsers as $staff) {
            if ($staff->id === $updatedByUserId) continue;
            Notification::createNotification(
                $staff->id,
                'recommended_beneficiary',
                'Barangay Recommendation Updated',
                $message,
                'recommended_beneficiary',
                $recommendation->id
            );
        }
    }

    /**
     * Create notification when a barangay partner recommendation is viewed by staff
     */
    public static function barangayRecommendationViewed($recommendationId): void
    {
        $recommendation = RecommendedBeneficiary::with('barangay')->find($recommendationId);
        if (!$recommendation) {
            return;
        }

        $fullName = trim("{$recommendation->first_name} {$recommendation->middle_name} {$recommendation->last_name}");
        Notification::createNotification(
            $recommendation->submitted_by,
            'recommended_beneficiary_viewed',
            'Recommendation Viewed',
            "Your recommendation for {$fullName} has been viewed by staff.",
            'recommended_beneficiary',
            $recommendation->id
        );
    }

    /**
     * Create notification when a barangay partner recommendation is rejected by staff
     */
    public static function barangayRecommendationRejected($recommendationId): void
    {
        $recommendation = RecommendedBeneficiary::with('barangay')->find($recommendationId);
        if (!$recommendation) {
            return;
        }

        $fullName = trim("{$recommendation->first_name} {$recommendation->middle_name} {$recommendation->last_name}");
        Notification::createNotification(
            $recommendation->submitted_by,
            'recommended_beneficiary_rejected',
            'Recommendation Rejected',
            "Your recommendation for {$fullName} has been rejected by staff.",
            'recommended_beneficiary',
            $recommendation->id
        );
    }

    /**
     * Create notification when a barangay partner recommendation is interviewed by staff
     */
    public static function barangayRecommendationInterviewed($recommendationId): void
    {
        $recommendation = RecommendedBeneficiary::with('barangay')->find($recommendationId);
        if (!$recommendation) {
            return;
        }

        $fullName = trim("{$recommendation->first_name} {$recommendation->middle_name} {$recommendation->last_name}");
        Notification::createNotification(
            $recommendation->submitted_by,
            'recommended_beneficiary_interviewed',
            'Recommendation Interviewed',
            "Your recommendation for {$fullName} has been interviewed by staff.",
            'recommended_beneficiary',
            $recommendation->id
        );
    }

    /**
     * Create notification when a barangay submits feedback for a relief operation
     */
    public static function barangayFeedbackSubmitted($feedbackId, $submittedByUserId = null): void
    {
        $feedback = ReliefOperationFeedback::with(['barangay', 'reliefOperation.calamity'])->find($feedbackId);
        if (!$feedback) {
            return;
        }

        $barangayName = $feedback->barangay?->name ?? 'Unknown Barangay';
        $reliefName = $feedback->reliefOperation?->calamity?->name ?? 'Relief Operation';
        $message = "New feedback submitted from {$barangayName} for {$reliefName}: {$feedback->message}";

        $staffUsers = self::getStaffUsers();
        foreach ($staffUsers as $staff) {
            if ($submittedByUserId && $staff->id === $submittedByUserId) {
                continue;
            }
            Notification::createNotification(
                $staff->id,
                'relief_operation_feedback',
                'Relief Operation Feedback',
                $message,
                'relief_operation_feedback',
                $feedback->id
            );
        }
    }

    /**
     * Create notification when inventory is added
     */
    public static function inventoryAdded($itemId, $addedByUserId): void
    {
        $item = Item::with('inventory')->find($itemId);
        if (!$item) return;

        $inventory = $item->inventory;
        $relatedId = $inventory ? $inventory->id : null;
        $itemName = $item->name;

        // Notify admin users
        $adminUsers = self::getAdminUsers();
        foreach ($adminUsers as $admin) {
            if ($admin->id === $addedByUserId) continue;
            Notification::createNotification(
                $admin->id,
                'inventory_addition',
                'New Inventory Added',
                "New inventory '{$itemName}' has been added",
                'inventory',
                $relatedId
            );
        }

        // Notify staff users as well
        $staffUsers = self::getStaffUsers();
        foreach ($staffUsers as $staff) {
            if ($staff->id === $addedByUserId) continue;
            Notification::createNotification(
                $staff->id,
                'inventory_addition',
                'Inventory Updated',
                "New inventory '{$itemName}' has been added",
                'inventory',
                $relatedId
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
        $adminUsers = self::getAdminUsers();
        
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
        $event = Event::find($eventId);
        if (!$event) return;

        // Notify admin users
        $adminUsers = self::getAdminUsers();
        
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
     * Create notification when a location request is submitted
     */
    public static function locationRequestSubmitted($requestId, $requestedByUserId = null): void
    {
        $locationRequest = LocationRequest::find($requestId);
        if (!$locationRequest) return;

        $adminUsers = self::getAdminUsers();
        foreach ($adminUsers as $admin) {
            Notification::createNotification(
                $admin->id,
                'location_request_submitted',
                'Location Request Submitted',
                "A new {$locationRequest->type} request for '{$locationRequest->name}' has been submitted",
                'location_request',
                $locationRequest->id
            );
        }

        $staffUsers = self::getStaffUsers();
        foreach ($staffUsers as $staff) {
            if ($requestedByUserId && $staff->id === $requestedByUserId) continue;
            Notification::createNotification(
                $staff->id,
                'location_request_submitted',
                'Location Request Submitted',
                "A new {$locationRequest->type} request for '{$locationRequest->name}' has been submitted",
                'location_request',
                $locationRequest->id
            );
        }
    }

    /**
     * Create notification when a location request is updated
     */
    public static function locationRequestUpdated($requestId, $updatedByUserId = null): void
    {
        $locationRequest = LocationRequest::find($requestId);
        if (!$locationRequest) return;

        $adminUsers = self::getAdminUsers();
        foreach ($adminUsers as $admin) {
            if ($updatedByUserId && $admin->id === $updatedByUserId) continue;
            Notification::createNotification(
                $admin->id,
                'location_request_updated',
                'Location Request Updated',
                "The {$locationRequest->type} request for '{$locationRequest->name}' has been updated",
                'location_request',
                $locationRequest->id
            );
        }

        $staffUsers = self::getStaffUsers();
        foreach ($staffUsers as $staff) {
            if ($updatedByUserId && $staff->id === $updatedByUserId) continue;
            Notification::createNotification(
                $staff->id,
                'location_request_updated',
                'Location Request Updated',
                "The {$locationRequest->type} request for '{$locationRequest->name}' has been updated",
                'location_request',
                $locationRequest->id
            );
        }
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
