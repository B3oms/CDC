<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReliefOperation extends Model
{
    protected $table = 'relief_operations';

    protected $fillable = [
        'calamity_id',
        'barangay_id',
        'relief_type_id',
        'operation_date',
        'status',
        'created_by',
    ];

    public function calamity()
    {
        return $this->belongsTo(Calamity::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }
}