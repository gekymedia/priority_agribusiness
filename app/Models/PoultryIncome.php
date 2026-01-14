<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoultryIncome extends Model
{
    use HasFactory;

    protected $fillable = [
        'bird_batch_id',
        'source',
        'amount',
        'date',
        'description',
    ];

    protected $dates = ['date'];

    public function birdBatch(): BelongsTo
    {
        return $this->belongsTo(BirdBatch::class);
    }
}