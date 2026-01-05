<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'generic_name',
        'dosage_type',
        'category',
        'strength',
        'threshold',
        'is_prescription',
        'stock',
        'is_active',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
