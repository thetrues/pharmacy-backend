<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNote extends Model
{
    protected $table = 'good_received_notes';
    protected $fillable = [
        'purchase_order_id',
        'received_date',
        'remarks',
        'received_by',
    ];
}
