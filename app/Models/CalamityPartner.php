<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalamityPartner extends Model
{
    protected $table = 'calamity_partners';

    protected $fillable = ['calamity_id', 'barangay_id'];

    public function calamity()
    {
        return $this->belongsTo(Calamity::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }
}