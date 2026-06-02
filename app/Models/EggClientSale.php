<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EggClientSale extends Model
{
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PARTIAL = 'partial';

    protected $fillable = [
        'bird_batch_id',
        'date',
        'buyer_name',
        'buyer_contact',
        'amount_paid',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function birdBatch(): BelongsTo
    {
        return $this->belongsTo(BirdBatch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EggSale::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->items->sum(fn (EggSale $item) => $item->line_total);
    }

    public function getBalanceAttribute(): float
    {
        return max(0, round($this->total_amount - (float) $this->amount_paid, 2));
    }

    public function getPaymentStatusAttribute(): string
    {
        $total = $this->total_amount;
        $paid = (float) $this->amount_paid;

        if ($total <= 0) {
            return self::PAYMENT_PAID;
        }

        if ($paid <= 0) {
            return self::PAYMENT_UNPAID;
        }

        if ($paid >= $total) {
            return self::PAYMENT_PAID;
        }

        return self::PAYMENT_PARTIAL;
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_UNPAID => 'Unpaid',
            self::PAYMENT_PARTIAL => 'Partial',
            default => ucfirst($this->payment_status),
        };
    }

    public function getSizeSummaryAttribute(): string
    {
        return $this->items
            ->groupBy(fn (EggSale $item) => $item->egg_size ?? 'other')
            ->map(fn ($group, $size) => $group->sum('quantity_sold') . ' ' . ucfirst($size))
            ->implode(', ');
    }
}
