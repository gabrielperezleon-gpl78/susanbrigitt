<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseLog extends Model
{
    protected $fillable = [
        'purchase_id',
        'user_id',
        'action',
        'reason',
        'previous_data',
    ];

    protected $casts = [
        'previous_data' => 'array',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
