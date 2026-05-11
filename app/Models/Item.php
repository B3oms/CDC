<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'name',
        'description',
        'unit',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
}