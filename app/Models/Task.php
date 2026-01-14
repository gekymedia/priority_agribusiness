<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'related_type',
        'related_id',
        'title',
        'description',
        'due_date',
        'status',
        'priority',
        'created_by',
        'assigned_to',
        'completed_at',
        'blacktask_task_id',
    ];

    protected $dates = [
        'due_date',
        'completed_at',
    ];

    /**
     * The user that created the task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Polymorphic relation to the related model.
     */
    public function related()
    {
        return $this->morphTo(__FUNCTION__, 'related_type', 'related_id');
    }

    /**
     * The employee assigned to this task.
     */
    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }
}