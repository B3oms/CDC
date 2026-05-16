<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'related_type',
        'related_id',
        'read',
    ];

    protected $casts = [
        'read' => 'boolean',
        'related_id' => 'integer',
    ];

    /**
     * Get the user who owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        return $this->update(['read' => true]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): bool
    {
        return $this->update(['read' => false]);
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope to get read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Scope to get notifications by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Create a new notification
     */
    public static function createNotification($userId, $type, $title, $message, $relatedType = null, $relatedId = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ]);
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'portal_open' => 'fas fa-door-open',
            'barangay_report' => 'fas fa-exclamation-triangle',
            'inventory_addition' => 'fas fa-box',
            'beneficiary_addition' => 'fas fa-users',
            'recommended_beneficiary' => 'fas fa-star',
            'recommended_beneficiary_viewed' => 'fas fa-eye',
            'recommended_beneficiary_rejected' => 'fas fa-times-circle',
            'recommended_beneficiary_interviewed' => 'fas fa-comments',
            'relief_operation_feedback' => 'fas fa-comments',
            'event_creation' => 'fas fa-calendar-alt',
            'location_request_approved' => 'fas fa-check-circle',
            'location_request_rejected' => 'fas fa-times-circle',
        ];

        return $icons[$this->type] ?? 'fas fa-bell';
    }

    /**
     * Get notification color based on type
     */
    public function getColorAttribute(): string
    {
        $colors = [
            'portal_open' => 'info',
            'barangay_report' => 'warning',
            'inventory_addition' => 'success',
            'beneficiary_addition' => 'primary',
            'recommended_beneficiary' => 'warning',
            'recommended_beneficiary_viewed' => 'info',
            'recommended_beneficiary_rejected' => 'danger',
            'recommended_beneficiary_interviewed' => 'success',
            'relief_operation_feedback' => 'primary',
            'event_creation' => 'secondary',
            'location_request_approved' => 'success',
            'location_request_rejected' => 'danger',
        ];

        return $colors[$this->type] ?? 'secondary';
    }
}
