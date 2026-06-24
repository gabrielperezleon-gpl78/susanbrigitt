<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'exchange_rate_id',
        'sale_date',
        'customer_name',
        'total_usd',
        'exchange_rate_value',
        'total_bs',
        'estimated_profit_usd',
        'rate_source',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_usd' => 'decimal:2',
        'exchange_rate_value' => 'decimal:4',
        'total_bs' => 'decimal:2',
        'estimated_profit_usd' => 'decimal:2',
    ];

    public function exchangeRate(): BelongsTo
    {
        return $this->belongsTo(ExchangeRate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'movementable_id')
            ->where('movementable_type', self::class);
    }
}
