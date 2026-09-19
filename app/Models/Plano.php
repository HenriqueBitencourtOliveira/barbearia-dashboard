<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plano extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_in_days',
        'is_active',
    ];

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }
}