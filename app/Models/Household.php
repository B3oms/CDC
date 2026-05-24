<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Household extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_id',
        'created_by',
        'head_of_household',
        'age',
        'sex',
        'birthdate',
        'contact_number',
        'is_cdc_beneficiary',
        'address',
        'status',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'is_cdc_beneficiary' => 'boolean',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(HouseholdMember::class);
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalMembersAttribute(): int
    {
        return $this->members()->count() + 1; // +1 for head of household
    }

    public function getFormattedBirthdateAttribute(): string
    {
        return $this->birthdate->format('F d, Y');
    }

    public function getAgeAttribute(): int
    {
        return $this->birthdate->age;
    }
}
