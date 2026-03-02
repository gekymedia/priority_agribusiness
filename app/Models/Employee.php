<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'employees';

    protected $fillable = [
        'user_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'access_level',
        'farm_id',
        'house_id',
        'hire_date',
        'address',
        'notes',
        'is_active',
        'status',
        'password',
        'base_salary',
        'allowances',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_branch',
        'bank_swift_or_sort',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'allowances' => 'array',
        'base_salary' => 'decimal:2',
    ];

    /**
     * Get the full name of the employee.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user associated with this employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the farm assigned to this employee.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the house assigned to this employee.
     */
    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    /**
     * Check if employee has a specific access level.
     */
    public function hasAccessLevel(string $level): bool
    {
        $levels = ['viewer' => 1, 'caretaker' => 2, 'manager' => 3, 'admin' => 4];
        return ($levels[$this->access_level] ?? 0) >= ($levels[$level] ?? 0);
    }

    /**
     * Check if employee is admin.
     */
    public function isAdmin(): bool
    {
        return $this->access_level === 'admin';
    }

    /**
     * Check if employee has been approved by admin (can log in and use the system).
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Scope to only approved employees.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to only pending employees.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if employee is manager or above.
     */
    public function isManager(): bool
    {
        return $this->hasAccessLevel('manager');
    }

    /**
     * Check if employee can edit.
     */
    public function canEdit(): bool
    {
        return $this->hasAccessLevel('caretaker');
    }

    /**
     * Get tasks assigned to this employee.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Get payrolls for this employee.
     */
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Calculate total allowances from the allowances array.
     */
    public function currentAllowancesTotal(): float
    {
        $allowances = $this->allowances ?? [];
        return array_sum(array_map('floatval', $allowances));
    }
}
