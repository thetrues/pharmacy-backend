<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order\OrderItem;
use App\Models\Customer\Customer;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'order_number',
        'customer_id',
        'order_date',
        'total_amount',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
