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
            'portal_open'               => 'fas fa-door-open',
            'barangay_report'           => 'fas fa-exclamation-triangle',
            'inventory_addition'        => 'fas fa-box',
            'beneficiary_addition'      => 'fas fa-users',
            'event_creation'            => 'fas fa-calendar-alt',
            'location_request_approved' => 'fas fa-check-circle',
            'location_request_rejected' => 'fas fa-times-circle',
            'recommendation_submitted'  => 'fas fa-hand-point-up',
            'recommendation_converted'  => 'fas fa-user-check',
            'recommendation_rejected'   => 'fas fa-user-times',
            'calamity_opened'           => 'fas fa-bolt',
            'calamity_created'          => 'fas fa-bolt',
            'inventory_updated'         => 'fas fa-boxes',
            'stock_low'                 => 'fas fa-exclamation-circle',
            'expiry_soon'               => 'fas fa-clock',
        ];

        return $icons[$this->type] ?? 'fas fa-bell';
    }

    /**
     * Get notification color based on type
     */
    public function getColorAttribute(): string
    {
        $colors = [
            'portal_open'               => '#3b82f6',
            'barangay_report'           => '#f59e0b',
            'inventory_addition'        => '#10b981',
            'beneficiary_addition'      => '#6366f1',
            'event_creation'            => '#8b5cf6',
            'location_request_approved' => '#059669',
            'location_request_rejected' => '#dc2626',
            'recommendation_submitted'  => '#f59e0b',
            'recommendation_converted'  => '#059669',
            'recommendation_rejected'   => '#dc2626',
            'calamity_opened'           => '#ef4444',
            'calamity_created'          => '#f59e0b',
            'inventory_updated'         => '#3b82f6',
            'stock_low'                 => '#ef4444',
            'expiry_soon'               => '#f97316',
        ];

        return $colors[$this->type] ?? '#6b7280';
    }
}
