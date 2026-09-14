<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PoultryExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'bird_batch_id',
        'farm_id',
        'category_id',
        'category', // Keep for backward compatibility
        'amount',
        'date',
        'description',
        'external_transaction_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function birdBatch(): BelongsTo
    {
        return $this->belongsTo(BirdBatch::class);
    }

    /**
     * Batches this expense applies to (supports shared feed across multiple batches).
     */
    public function birdBatches(): BelongsToMany
    {
        return $this->belongsToMany(BirdBatch::class, 'poultry_expense_bird_batch')
            ->withTimestamps();
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Expense category (uses category_id FK).
     *
     * Note: The legacy DB column is also named `category` (string). We avoid naming
     * this relationship `category()` to prevent collisions with the attribute.
     */
    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    /**
     * Human-readable batch label(s) for lists and detail views.
     */
    public function batchDisplayLabel(string $empty = 'General'): string
    {
        if ($this->relationLoaded('birdBatches') && $this->birdBatches->isNotEmpty()) {
            return $this->birdBatches->pluck('batch_code')->filter()->implode(', ');
        }

        return $this->birdBatch?->batch_code ?? $empty;
    }
}