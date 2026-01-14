<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropInputExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'planting_id',
        'date',
        'category',
        'description',
        'amount',
    ];

    protected $dates = ['date'];

    public function planting(): BelongsTo
    {
        return $this->belongsTo(Planting::class);
    }
}