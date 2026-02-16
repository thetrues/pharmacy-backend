<?php

namespace App\Models\Order;

use App\Models\Sales;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $table = 'order_payments';
    protected $fillable = [
        'sales_id',
        'payment_method',
        'cash_amount',
        'card_amount',
        'credit_amount',
        'change',
        'amount_paid',
        'total_amount',
        'payment_date',
        'cashier_id',
        'shift_id',
        'order_number',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }
}
