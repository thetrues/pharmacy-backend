<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
