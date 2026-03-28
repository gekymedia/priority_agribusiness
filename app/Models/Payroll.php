<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'pay_period',
        'base_salary',
        'allowances_total',
        'deductions_total',
        'net_pay',
        'status',
        'paid_at',
        'notes',
        'poultry_expense_id',
    ];

    protected $casts = [
        'pay_period' => 'date',
        'paid_at' => 'datetime',
        'base_salary' => 'decimal:2',
        'allowances_total' => 'decimal:2',
        'deductions_total' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryExpense(): BelongsTo
    {
        return $this->belongsTo(PoultryExpense::class, 'poultry_expense_id');
    }
}
