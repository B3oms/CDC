<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['name', 'description', 'color'];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function items()
    {
        return $this->hasManyThrough(Item::class, Subcategory::class);
    }
}