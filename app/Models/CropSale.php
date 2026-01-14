<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'planting_id',
        'date',
        'quantity_sold',
        'price_per_unit',
        'buyer_name',
        'buyer_contact',
        'notes',
    ];

    protected $dates = ['date'];

    public function planting(): BelongsTo
    {
        return $this->belongsTo(Planting::class);
    }
}