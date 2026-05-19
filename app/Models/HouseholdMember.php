<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HouseholdMember extends Model
{
    protected $fillable = [
        'household_request_id',
        'name',
        'age',
        'sex',
    ];

    public function householdRequest()
    {
        return $this->belongsTo(HouseholdRequest::class);
    }
}
