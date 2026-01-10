<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class CustomerCredit extends Model
{
    protected $table = 'customer_credits';
    protected $fillable = [
        'customer_id',
        'credit_amount',
        'credit_date',
        'remarks',
        'remaining_credit',
        'created_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
