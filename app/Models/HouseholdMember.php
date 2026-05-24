<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'name',
        'age',
        'sex',
        'relationship_to_head',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }
}
