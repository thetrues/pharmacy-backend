<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'orderNumber',
        'customer_id',
        'customerName',
        'subtotalAmount',
        'taxAmount',
        'totalAmount',
        'status',
        'created_by'
    ];

    public function items()
    {
        return $this->hasMany(SalesItems::class, 'sale_id')->with('product');
    }
}
