<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BirdBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'house_id',
        'batch_code',
        'breed',
        'purpose',
        'arrival_date',
        'quantity_arrived',
        'cost_per_bird',
        'supplier_name',
        'status',
    ];

    protected $casts = [
        'arrival_date' => 'date',
    ];

    /**
     * The farm that owns the batch.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * The house/pen in which the batch resides.
     */
    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    /**
     * Daily or periodic records for this batch.
     */
    public function dailyRecords(): HasMany
    {
        return $this->hasMany(BirdBatchRecord::class);
    }

    /**
     * Medication or vaccination records associated with this batch.
     */
    public function medicationRecords(): HasMany
    {
        return $this->hasMany(MedicationRecord::class);
    }

    /**
     * Medication schedules for this batch.
     */
    public function medicationSchedules(): HasMany
    {
        return $this->hasMany(MedicationSchedule::class);
    }

    /**
     * Egg production entries for this batch.
     */
    public function eggProductions(): HasMany
    {
        return $this->hasMany(EggProduction::class);
    }

    /**
     * Bird sales for this batch.
     */
    public function birdSales(): HasMany
    {
        return $this->hasMany(BirdSale::class);
    }

    /**
     * Egg sales for this batch.
     */
    public function eggSales(): HasMany
    {
        return $this->hasMany(EggSale::class);
    }

    /**
     * Expenses associated with this batch.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(PoultryExpense::class);
    }

    /**
     * Income entries associated with this batch.
     */
    public function incomes(): HasMany
    {
        return $this->hasMany(PoultryIncome::class);
    }
}