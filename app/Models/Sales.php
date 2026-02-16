<?php

namespace App\Models;

use App\Models\Order\OrderPayment;
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

    public function payments()
    {
        return $this->hasMany(OrderPayment::class, 'sales_id');
    }
}
