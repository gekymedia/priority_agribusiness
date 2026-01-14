<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'bird_batch_id',
        'date',
        'medication_name',
        'dosage',
        'quantity_used',
        'cost',
        'purpose',
        'notes',
    ];

    protected $dates = [
        'date',
    ];

    public function birdBatch(): BelongsTo
    {
        return $this->belongsTo(BirdBatch::class);
    }
}