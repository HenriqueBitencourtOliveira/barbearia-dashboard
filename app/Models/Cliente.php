<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'plano_id',
        'barber',
        'cuts_used',
        'subscription_start',
        'subscription_end',
        'status',
    ];

    public function plano(): BelongsTo
    {
        return $this->belongsTo(Plano::class);
    }

    public function cortes(): HasMany
    {
        return $this->hasMany(ClienteCorte::class);
    }
}