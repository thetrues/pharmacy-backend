<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer\CustomerCredit;
class Customer extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'loyalty_points',
        'membership_level',
        'credit_limit',
        'current_credit',
    ];

    public function credits()
    {
        return $this->hasMany(CustomerCredit::class, 'customer_id');
    }

    
}
