<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Household;

class Calamity extends Model
{
    protected $table = 'calamities';

    protected $fillable = [
        'name',
        'type',
        'description',
        'date_occurred',
        'status',
        'created_by',
    ];

    protected $casts = [
        'date_occurred' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function reliefOperations()
    {
        return $this->hasMany(ReliefOperation::class);
    }

    public function partners()
    {
        return $this->hasMany(CalamityPartner::class);
    }

    public function barangays()
    {
        return $this->belongsToMany(
            Barangay::class,
            'calamity_partners'
        );
    }

    public function evacuationCenters()
    {
        return $this->hasMany(EvacuationCenter::class);
    }

    public function evacuationReports()
    {
        return $this->hasMany(EvacuationReport::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Computed Attribute
    |--------------------------------------------------------------------------
    */

    public function getPriorityLevelAttribute(): string
    {
        $reliefOperations = $this->reliefOperations()
            ->with(['eventBarangays.beneficiary'])
            ->get();

        $evacuees = 0;
        $affectedHouseholds = 0;
        $beneficiaryRecords = 0;

        foreach ($reliefOperations as $operation) {
            foreach ($operation->eventBarangays as $barangay) {
                $evacuees += $barangay->evacuees ?? 0;
                $affectedHouseholds += $barangay->affected_households ?? 0;

                foreach ($barangay->beneficiary as $beneficiary) {
                    $beneficiaryRecords++;
                }
            }
        }

        $existingHouseholds = HouseholdRequest::count();

        if ($evacuees >= 1000 || $affectedHouseholds >= 5000) {
            return 'High';
        } elseif ($evacuees >= 500 || $affectedHouseholds >= 2000) {
            return 'Moderate';
        } elseif ($evacuees >= 100 || $affectedHouseholds >= 500) {
            return 'Low';
        }

        return 'Low';
    }
}