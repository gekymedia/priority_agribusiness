<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'farm_type',
    ];

    /**
     * Get the houses associated with the farm.
     */
    public function houses(): HasMany
    {
        return $this->hasMany(House::class);
    }

    /**
     * Get the fields (crop plots) associated with the farm.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    /**
     * Expenses associated with the farm (excluding specific batch expenses).
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(PoultryExpense::class);
    }

    /**
     * Employees assigned to this farm.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}