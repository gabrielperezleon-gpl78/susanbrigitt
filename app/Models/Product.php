<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'initial_stock' => 'integer',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
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
}
