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
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function birdBatch(): BelongsTo
    {
        return $this->belongsTo(BirdBatch::class);
    }
}