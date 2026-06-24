<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id',
        'movementable_id',
        'movementable_type',
        'type',
        'quantity',
        'stock_after_movement',
        'movement_date',
        'notes',
    ];

    protected $casts = [
        'movement_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function movementable(): MorphTo
    {
        return $this->morphTo();
    }
}
