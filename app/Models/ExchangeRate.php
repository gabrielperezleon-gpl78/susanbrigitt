<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'rate_date',
        'bcv_rate',
        'binance_rate',
        'manual_rate',
        'used_rate',
        'source',
        'status',
        'notes',
    ];

    protected $casts = [
        'rate_date' => 'date',
        'bcv_rate' => 'decimal:4',
        'binance_rate' => 'decimal:4',
        'manual_rate' => 'decimal:4',
        'used_rate' => 'decimal:4',
    ];
}
