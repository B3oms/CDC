<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by',
        'type',
        'municipality_id',
        'name',
        'status',
        'remarks',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user who made the request
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the admin who approved the request
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the associated municipality
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Scope to get pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to get municipality requests
     */
    public function scopeMunicipality($query)
    {
        return $query->where('type', 'municipality');
    }

    /**
     * Scope to get barangay requests
     */
    public function scopeBarangay($query)
    {
        return $query->where('type', 'barangay');
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="status-badge pending">Pending</span>',
            'approved' => '<span class="status-badge approved">Approved</span>',
            'rejected' => '<span class="status-badge rejected">Rejected</span>',
        ];

        return $badges[$this->status] ?? '';
    }

    /**
     * Get type badge HTML
     */
    public function getTypeBadgeAttribute(): string
    {
        $badges = [
            'municipality' => '<span class="type-badge municipality">Municipality</span>',
            'barangay' => '<span class="type-badge barangay">Barangay</span>',
        ];

        return $badges[$this->type] ?? '';
    }

    /**
     * Check if request can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request can be rejected
     */
    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request can be edited
     */
    public function canBeEdited(): bool
    {
        return $this->status === 'pending' && $this->requested_by === auth()->id();
    }
}
