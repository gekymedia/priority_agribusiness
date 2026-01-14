<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'bird_batch_id',
        'medication_calendar_id',
        'start_date',
        'week_number',
        'medication_name',
        'description',
        'dosage',
        'method',
        'scheduled_date',
        'is_completed',
        'completed_at',
        'task_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'scheduled_date' => 'date',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    /**
     * Get the bird batch this schedule belongs to.
     */
    public function birdBatch(): BelongsTo
    {
        return $this->belongsTo(BirdBatch::class);
    }

    /**
     * Get the medication calendar this schedule uses.
     */
    public function medicationCalendar(): BelongsTo
    {
        return $this->belongsTo(MedicationCalendar::class);
    }

    /**
     * Get the task associated with this medication schedule.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
