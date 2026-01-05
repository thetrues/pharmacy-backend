<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'product_id',
        'quantity',
        'SKU',
        'supplier',
        'batch_number',
        'expiry_date',
        'reorder_level',
        'stock',
        'cost_price',
        'selling_price',
        'is_active',
        'added_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}