<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'exchange_rate_id',
        'purchase_date',
        'total_usd',
        'exchange_rate_value',
        'total_bs',
        'rate_source',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_usd' => 'decimal:2',
        'exchange_rate_value' => 'decimal:4',
        'total_bs' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function exchangeRate(): BelongsTo
    {
        return $this->belongsTo(ExchangeRate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'movementable_id')
            ->where('movementable_type', self::class);
    }
}
