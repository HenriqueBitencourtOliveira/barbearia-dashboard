<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'amount',
        'payment_method',
        'payment_id',
        'category',
        'external_reference',
        'status',
        'sold_at',
        'barber',
    ];

    // Casts para garantir que 'sold_at' seja tratado como datetime e 'amount' como decimal
    protected $casts = [
        'sold_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function itens()
    {
        return $this->hasMany(ItemVenda::class);
    }
}
