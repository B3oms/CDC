<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReliefEventDistributedItem extends Model
{
    protected $table = 'relief_event_distributed_items';

    protected $fillable = [
        'relief_event_id',
        'item_id',
        'total_quantity',
        'per_beneficiary',
        'beneficiaries_count',
        'unit',
    ];

    public function reliefEvent()
    {
        return $this->belongsTo(ReliefEvent::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
