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
        'civil_status',
        'age',
        'is_4ps_member',
        'birthdate',
        'contact_number',
        'address',
        'occupation',
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
        'indigenous_group',
        'is_pwd',
        'pwd_type',
        // Family background fields
        'spouse_name',
        'spouse_age',
        'spouse_sex',
        'spouse_birthdate',
        'spouse_occupation',
        'children',
        'status',
        'rejection_date',
        'scheduled_deletion_date',
    ];

    protected $casts = [
        'is_4ps_member' => 'boolean',
        'has_senior'     => 'boolean',
        'interviewed_at' => 'datetime',
        'is_pwd'         => 'boolean',
        'children'       => 'array',
        'rejection_date' => 'date',
        'scheduled_deletion_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($beneficiary) {
            if (empty($beneficiary->unique_id)) {
                $beneficiary->unique_id = self::generateUniqueId();
            }
        });
    }

    public static function generateUniqueId(): string
    {
        do {
            // Generate a random ID with format: BE-URAN-Y67W
            // First part: BE (Beneficiary)
            $prefix = 'BE';
            
            // Second part: 4 random letters (like URAN)
            $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $middlePart = substr(str_shuffle($letters), 0, 4);
            
            // Third part: 4 random characters (letters and numbers, like Y67W)
            $alphanumeric = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $suffix = substr(str_shuffle($alphanumeric), 0, 4);
            
            $uniqueId = "{$prefix}-{$middlePart}-{$suffix}";
        } while (self::where('unique_id', $uniqueId)->exists());

        return $uniqueId;
    }

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