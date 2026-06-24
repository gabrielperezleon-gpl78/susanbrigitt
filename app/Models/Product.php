<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'brand_id',
        'tone_id',
        'supplier_id',
        'internal_code',
        'name',
        'slug',
        'barcode',
        'description',
        'image_path',
        'purchase_price_usd',
        'sale_price_usd',
        'unit_profit_usd',
        'profit_margin',
        'initial_stock',
        'current_stock',
        'minimum_stock',
        'entry_date',
        'status',
        'internal_notes',
    ];

    protected $casts = [
        'purchase_price_usd' => 'decimal:2',
        'sale_price_usd' => 'decimal:2',
        'unit_profit_usd' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'entry_date' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function tone(): BelongsTo
    {
        return $this->belongsTo(Tone::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'agotado';
        }

        if ($this->current_stock <= $this->minimum_stock) {
            return 'stock_bajo';
        }

        return 'disponible';
    }
}
