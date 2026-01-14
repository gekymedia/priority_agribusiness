<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function birdBatch(): BelongsTo
    {
        return $this->belongsTo(BirdBatch::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }
}