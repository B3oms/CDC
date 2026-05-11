<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalamityReport extends Model
{
    protected $table = 'calamity_reports';

    protected $fillable = [
        'calamity_id',
        'calamity_name',
        'calamity_type',
        'date_occurred',
        'total_evacuees',
        'total_households',
        'total_beneficiaries',
        'affected_areas',
        'report_generated_at',
        'generated_by',
    ];

    protected $casts = [
        'date_occurred' => 'datetime',
        'report_generated_at' => 'datetime',
    ];

    public function calamity()
    {
        return $this->belongsTo(Calamity::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
