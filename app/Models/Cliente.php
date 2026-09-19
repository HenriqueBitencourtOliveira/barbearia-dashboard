<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'plano_id',
        'subscription_start',
        'subscription_end',
        'status',
    ];

    public function plano(): BelongsTo
    {
        return $this->belongsTo(Plano::class);
    }
}