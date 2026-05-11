<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReliefEventFacilitator extends Model
{
    protected $table = 'relief_event_facilitators';

    protected $fillable = [
        'relief_event_id',
        'user_id',
    ];

    public function reliefEvent()
    {
        return $this->belongsTo(ReliefEvent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}