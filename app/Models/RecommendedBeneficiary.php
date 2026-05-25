<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendedBeneficiary extends Model
{
    protected $table = 'recommended_beneficiaries';

    protected $fillable = [
        'barangay_id',
        'submitted_by',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'age',
        'contact_number',
        'address',
        'status',
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}