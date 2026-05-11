<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $table = 'municipalities';

    protected $fillable = [
        'name',
        'province', 
        'status',
        'notes'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Scopes for filtering by status
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Get status badge HTML
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Pending</span>',
            'approved' => '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Approved</span>',
            'rejected' => '<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Rejected</span>',
        ];

        return $badges[$this->status] ?? '';
    }

    public function barangays()
    {
        return $this->hasMany(Barangay::class);
    }

    public function locationRequests()
    {
        return $this->hasMany(LocationRequest::class);
    }
}