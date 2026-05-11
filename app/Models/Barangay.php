<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $table = 'barangays';

    protected $fillable = ['municipality_id', 'name'];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function locationRequests()
    {
        return $this->hasMany(LocationRequest::class);
    }
}