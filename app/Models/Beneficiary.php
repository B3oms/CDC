<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $table = 'beneficiaries';

    protected $fillable = [
        'user_id',
        'barangay_id',
        'unique_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'gender',
        'is_4ps_member',
        'birthdate',
        'contact_number',
        'address',
        'family_size',
        'monthly_income',
        'vulnerability_level',
        'has_senior',
        'children_count',
        'criteria_met',
        'interview_notes',
        'interviewed_by',
        'interviewed_at',
        'is_verified',
    ];

    protected $casts = [
        'is_4ps_member' => 'boolean',
        'has_senior'     => 'boolean',
        'interviewed_at' => 'datetime',
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewed_by');
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function reliefEvents()
    {
        return $this->hasMany(ReliefEventBeneficiary::class);
    }

    public function participatedReliefEvents()
    {
        return $this->belongsToMany(ReliefEvent::class, 'relief_event_beneficiaries')
            ->withPivot('barangay_id')
            ->withTimestamps();
    }

    // Auto-verification logic
    public static function checkCriteria($familySize, $monthlyIncome, $hasSenior, $childrenCount, $is4PsMember = false): int
    {
        $criteria = 0;
        if ($familySize >= 4)          $criteria++;
        if ($monthlyIncome <= 10000)   $criteria++;
        if ($hasSenior)                $criteria++;
        if ($childrenCount >= 2)       $criteria++;
        if ($is4PsMember)              $criteria++;
        return $criteria;
    }
}