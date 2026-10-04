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
        'cuts_included',
        'is_active',
    ];

    protected $casts = [
        'cuts_included' => 'integer',
        'is_active' => 'boolean',
    ];

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }
}