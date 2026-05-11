<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReliefEventBarangay extends Model
{
    protected $table = 'relief_event_barangays';

    protected $fillable = [
        'relief_event_id',
        'barangay_id',
        'municipality_id',
    ];

    public function reliefEvent()
    {
        return $this->belongsTo(ReliefEvent::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }
}