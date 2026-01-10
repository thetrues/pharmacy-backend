<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $table = 'order_payments';
    protected $fillable = [
        'order_id',
        'payment_method',
        'amount_paid',
        'payment_date',
        'transaction_reference',
        'cashier_id',
        'total_amount',
    ];
}
