<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EggProduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bird_batch_id',
        'date',
        'eggs_collected',
        'cracked_or_damaged',
        'eggs_used_internal',
        'egg_size_breakdown',
        'eggs_large',
        'eggs_medium',
        'eggs_small',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'egg_size_breakdown' => 'boolean',
    ];

    public function birdBatch(): BelongsTo
    {
        return $this->belongsTo(BirdBatch::class);
    }

    /** Good eggs available for sale (total minus cracked and internal use). */
    public function remainingEggs(): int
    {
        return max(0, (int) $this->eggs_collected - (int) $this->cracked_or_damaged - (int) $this->eggs_used_internal);
    }

    /** Sum of size buckets when breakdown is recorded. */
    public function sizeBreakdownSum(): int
    {
        return (int) $this->eggs_large + (int) $this->eggs_medium + (int) $this->eggs_small;
    }
}