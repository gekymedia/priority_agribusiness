<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicationCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'schedule',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'schedule' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get all medication schedules using this calendar.
     */
    public function medicationSchedules(): HasMany
    {
        return $this->hasMany(MedicationSchedule::class);
    }
}
