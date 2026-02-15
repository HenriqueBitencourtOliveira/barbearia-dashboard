<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemVenda extends Model
{
    protected $fillable = [
        'venda_id', 
        'name',
        'unit_price', 
        'quantity', 
        'category',
        'barber'
        ];
}
