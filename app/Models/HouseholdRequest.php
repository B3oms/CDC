<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HouseholdRequest extends Model
{
    protected $fillable = [
        'barangay_id',
        'head_of_household',
        'head_age',
        'head_sex',
        'birthday',
        'contact_number',
        'address',
        'family_size',
        'requested_by',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'birthday' => 'date',
        'approved_at' => 'datetime',
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function members()
    {
        return $this->hasMany(HouseholdMember::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function getFormattedContactNumberAttribute()
    {
        if (!$this->contact_number) {
            return null;
        }

        // Remove all non-digit characters
        $number = preg_replace('/[^0-9]/', '', $this->contact_number);
        
        // Handle Philippine number formats
        if (strlen($number) === 10) {
            // Local format: 09XX XXX XXXX
            return substr($number, 0, 4) . ' ' . substr($number, 4, 3) . ' ' . substr($number, 7);
        } elseif (strlen($number) === 11 && substr($number, 0, 1) === '0') {
            // Local format with leading 0: 09XX XXX XXXX
            return substr($number, 0, 4) . ' ' . substr($number, 4, 3) . ' ' . substr($number, 7);
        } elseif (strlen($number) === 11 && substr($number, 0, 2) === '63') {
            // International format: +63 XXX XXX XXXX
            return '+63 ' . substr($number, 2, 3) . ' ' . substr($number, 5, 3) . ' ' . substr($number, 8);
        } elseif (strlen($number) === 12 && substr($number, 0, 3) === '+63') {
            // International format with +: +63 XXX XXX XXXX
            return '+63 ' . substr($number, 3, 3) . ' ' . substr($number, 6, 3) . ' ' . substr($number, 9);
        } else {
            // Return original if it doesn't match expected formats
            return $this->contact_number;
        }
    }
}
