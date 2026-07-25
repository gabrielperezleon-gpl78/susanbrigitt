<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

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
        'status',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_usd' => 'decimal:2',
        'exchange_rate_value' => 'decimal:4',
        'total_bs' => 'decimal:2',
        'cancelled_at' => 'datetime',
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
        return $this->hasMany(
            InventoryMovement::class,
            'movementable_id'
        )->where(
            'movementable_type',
            self::class
        );
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PurchaseLog::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'cancelled_by'
        );
    }

    public function scopeConfirmed(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            self::STATUS_CONFIRMED
        );
    }

    public function scopeCancelled(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            self::STATUS_CANCELLED
        );
    }

    public function isCancelled(): bool
    {
        return $this->status
            === self::STATUS_CANCELLED;
    }
}
