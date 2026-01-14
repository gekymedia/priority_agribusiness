<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirdSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'bird_batch_id',
        'date',
        'quantity_sold',
        'price_per_bird',
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
}