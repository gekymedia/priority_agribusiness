<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirdBatchRecord extends Model
{
    use HasFactory;

    protected $table = 'bird_batch_records';

    protected $fillable = [
        'bird_batch_id',
        'record_date',
        'feed_used_kg',
        'water_used_litres',
        'mortality_count',
        'cull_count',
        'average_weight_kg',
        'notes',
    ];

    protected $dates = [
        'record_date',
    ];

    /**
     * Get the batch that owns this record.
     */
    public function birdBatch(): BelongsTo
    {
        return $this->belongsTo(BirdBatch::class);
    }
}