<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReliefEventBeneficiary extends Model
{
    protected $table = 'relief_event_beneficiaries';

    protected $fillable = [
        'relief_event_id',
        'barangay_id',
        'beneficiary_id',
    ];

    public function reliefEvent()
    {
        return $this->belongsTo(ReliefEvent::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}