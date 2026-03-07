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

    /** Human-readable status label for display. */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending (Not paid)',
            self::STATUS_PAID => 'Paid',
            self::STATUS_DELIVERED => 'Complete',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status ?? ''),
        };
    }

    public static function generateOrderNumber(): string
    {
        do {
            $num = 'EGG-' . strtoupper(substr(uniqid(), -6)) . '-' . date('ymd');
        } while (static::where('order_number', $num)->exists());

        return $num;
    }
}
