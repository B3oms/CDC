<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $table = 'distributions';

    protected $fillable = [
        'relief_operation_id',
        'beneficiary_id',
        'relief_package_id',
        'date_distributed',
        'status',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function reliefOperation()
    {
        return $this->belongsTo(ReliefOperation::class);
    }
}