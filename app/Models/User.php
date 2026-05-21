<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'role_id',
        'barangay_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'contact_number',
        'address',
        'password',
        'status',
        'position',
        'organization',
        'birthdate',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function beneficiary()
    {
        return $this->hasOne(Beneficiary::class);
    }
}