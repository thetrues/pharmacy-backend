<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNoteItem extends Model
{
    protected $table = 'grn_items';
    protected $fillable = [
        'grn_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];
}
