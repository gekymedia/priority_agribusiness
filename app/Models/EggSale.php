<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EggSale extends Model
{
    use HasFactory;

    public const SIZE_SMALL = 'small';
    public const SIZE_MEDIUM = 'medium';
    public const SIZE_LARGE = 'large';

    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_UNPAID = 'unpaid';

    protected $fillable = [
        'market_order_id',
        'egg_client_sale_id',
        'bird_batch_id',
        'date',
        'quantity_sold',
        'unit_type',
        'egg_size',
        'price_per_unit',
        'payment_status',
        'buyer_name',
        'buyer_contact',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function birdBatch(): BelongsTo
    {
        return $this->belongsTo(BirdBatch::class);
    }

    public function marketOrder(): BelongsTo
    {
        return $this->belongsTo(MarketOrder::class);
    }

    public function clientSale(): BelongsTo
    {
        return $this->belongsTo(EggClientSale::class, 'egg_client_sale_id');
    }

    public function getLineTotalAttribute(): float
    {
        return round((float) $this->quantity_sold * (float) $this->price_per_unit, 2);
    }

    public function getEggSizeLabelAttribute(): string
    {
        return $this->egg_size ? ucfirst($this->egg_size) : '—';
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return ucfirst($this->payment_status ?? self::PAYMENT_PAID);
    }

    public static function sizeOptions(): array
    {
        return [
            self::SIZE_SMALL => 'Small',
            self::SIZE_MEDIUM => 'Medium',
            self::SIZE_LARGE => 'Large',
        ];
    }
}
