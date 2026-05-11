<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvacuationCenter extends Model
{
    protected $table = 'evacuation_centers';

    protected $fillable = [
        'calamity_id',
        'barangay_id',
        'venue',
        'location',
    ];

    public function calamity()
    {
        return $this->belongsTo(Calamity::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function reports()
    {
        return $this->hasMany(EvacuationReport::class);
    }
}