<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'item_id',
        'quantity',
        'expiration_date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}