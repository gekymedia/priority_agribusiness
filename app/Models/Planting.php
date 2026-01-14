<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Planting extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'crop_name',
        'planting_date',
        'expected_harvest_date',
        'seed_source',
        'quantity_planted',
        'status',
    ];

    protected $dates = ['planting_date', 'expected_harvest_date'];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function inputs(): HasMany
    {
        return $this->hasMany(CropInputExpense::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CropActivity::class);
    }

    public function harvests(): HasMany
    {
        return $this->hasMany(Harvest::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(CropSale::class);
    }
}