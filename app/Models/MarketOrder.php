<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketOrder extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'delivery_address',
        'delivery_notes',
        'wants_delivery',
        'subtotal',
        'total_amount',
        'payment_gateway',
        'payment_reference',
        'status',
    ];

    protected $casts = [
        'wants_delivery' => 'boolean',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MarketOrderItem::class);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $num = 'EGG-' . strtoupper(substr(uniqid(), -6)) . '-' . date('ymd');
        } while (static::where('order_number', $num)->exists());

        return $num;
    }
}
