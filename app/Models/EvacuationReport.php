<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvacuationReport extends Model
{
    protected $table = 'evacuation_reports';

    protected $fillable = [
        'evacuation_center_id',
        'calamity_id',
        'barangay_id',
        'reported_by',
        'household_count',
        'household_ids',
        'evacuee_count',
        'severity_level',
        'ranking_score',
    ];

    protected $casts = [
        'household_ids' => 'array',
    ];

    public function evacuationCenter()
    {
        return $this->belongsTo(EvacuationCenter::class);
    }

    public function calamity()
    {
        return $this->belongsTo(Calamity::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}