<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemVenda extends Model
{
    protected $fillable = [
        'venda_id', 
        'nome',
        'preco_unitario', 
        'quantidade', 
        'categoria',
        'barber'
        ];
}
