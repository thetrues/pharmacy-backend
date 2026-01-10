<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'bank_account',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id');
    }


}
