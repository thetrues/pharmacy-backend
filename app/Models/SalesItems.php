<?php

namespace App\Models;

use App\Models\Sales;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class SalesItems extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unitPrice'
    ];

    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sale_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
