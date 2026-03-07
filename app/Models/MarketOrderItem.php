<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketOrderItem extends Model
{
    protected $fillable = [
        'market_order_id',
        'unit_type',
        'quantity',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function marketOrder(): BelongsTo
    {
        return $this->belongsTo(MarketOrder::class);
    }
}
