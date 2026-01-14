<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'name',
        'capacity',
        'type',
    ];

    /**
     * The farm that owns the house.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Batches of birds in this house.
     */
    public function batches(): HasMany
    {
        return $this->hasMany(BirdBatch::class);
    }

    /**
     * Employees assigned to this house.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}