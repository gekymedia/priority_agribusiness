<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Harvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'planting_id',
        'harvest_date',
        'quantity_harvested',
        'notes',
    ];

    protected $dates = ['harvest_date'];

    public function planting(): BelongsTo
    {
        return $this->belongsTo(Planting::class);
    }
}