<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    protected $table = 'cashier_shifts';
    protected $fillable = [
        'cashier_id',
        'shift_start',
        'shift_end',
        'starting_cash',
        'ending_cash',
        'return_cash',
        'status'
    ];
}
