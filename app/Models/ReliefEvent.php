<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReliefEvent extends Model
{
    protected $table = 'relief_events';

    protected $fillable = [
        'name',
        'date',
        'venue',
        'status',
        'calamity_id',
        'created_by',
    ];

    public function calamity()
    {
        return $this->belongsTo(Calamity::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function eventBarangays()
    {
        return $this->hasMany(ReliefEventBarangay::class);
    }

    public function barangays()
    {
        return $this->belongsToMany(Barangay::class, 'relief_event_barangays');
    }

    public function facilitators()
    {
        return $this->belongsToMany(User::class, 'relief_event_facilitators');
    }

    public function beneficiaries()
    {
        return $this->hasMany(ReliefEventBeneficiary::class);
    }
}