<?php

namespace App\Services;

use App\Models\ExpenseCategory;
use App\Models\Farm;
use App\Models\Payroll;
use App\Models\PoultryExpense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PayrollSalaryExpenseService
{
    /**
     * When payroll is paid, ensure a linked poultry expense exists and is synced to Priority Bank.
     * When not paid, remove the linked expense if any.
     */
    public function sync(Payroll $payroll): void
    {
        $payroll->loadMissing('employee');

        if ($payroll->status !== 'paid') {
            $this->detachExpense($payroll);
            return;
        }

        $category = $this->resolveLaborCategory();
        if (! $category) {
            Log::warning('Payroll salary expense skipped: no Labor expense category', ['payroll_id' => $payroll->id]);

            return;
        }

        $farmId = $payroll->employee?->farm_id ?? Farm::query()->orderBy('id')->value('id');
        if (! $farmId) {
            Log::warning('Payroll salary expense skipped: no farm available', ['payroll_id' => $payroll->id]);

            return;
        }

        $paidDate = $payroll->paid_at
            ? Carbon::parse($payroll->paid_at)->toDateString()
            : Carbon::parse($payroll->pay_period)->toDateString();

        $periodLabel = Carbon::parse($payroll->pay_period)->format('M Y');
        $employeeName = $payroll->employee?->full_name ?? 'Employee';
        $description = "Salary: {$employeeName} — {$periodLabel}";
        $externalId = 'agri_payroll_salary_' . $payroll->id;

        $expense = null;
        if ($payroll->poultry_expense_id) {
            $expense = PoultryExpense::find($payroll->poultry_expense_id);
        }
        if (! $expense) {
            $expense = PoultryExpense::where('external_transaction_id', $externalId)->first();
            if ($expense) {
                $payroll->forceFill(['poultry_expense_id' => $expense->id])->saveQuietly();
            }
        }

        $payload = [
            'farm_id' => $farmId,
            'bird_batch_id' => null,
            'category_id' => $category->id,
            'category' => $category->name,
            'amount' => (float) $payroll->net_pay,
            'date' => $paidDate,
            'description' => $description,
            'external_transaction_id' => $externalId,
        ];

        if ($expense) {
            $expense->update($payload);
        } else {
            $expense = PoultryExpense::create($payload);
            if (! $payroll->poultry_expense_id) {
                $payroll->forceFill(['poultry_expense_id' => $expense->id])->saveQuietly();
            }
        }

        try {
            (new PriorityBankIntegrationService())->pushPoultryExpense($expense->fresh());
        } catch (\Throwable $e) {
            Log::error('Failed to push payroll salary expense to Priority Bank', [
                'payroll_id' => $payroll->id,
                'poultry_expense_id' => $expense->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Call before deleting a payroll row to remove the linked expense without re-running paid logic.
     */
    public function detachForDeletion(Payroll $payroll): void
    {
        $this->detachExpense($payroll);
    }

    protected function detachExpense(Payroll $payroll): void
    {
        if (! $payroll->poultry_expense_id) {
            return;
        }

        $expenseId = $payroll->poultry_expense_id;
        $payroll->forceFill(['poultry_expense_id' => null])->saveQuietly();
        PoultryExpense::where('id', $expenseId)->delete();
    }

    protected function resolveLaborCategory(): ?ExpenseCategory
    {
        return ExpenseCategory::query()
            ->where('is_active', true)
            ->where('name', 'Labor')
            ->where('type', 'poultry')
            ->first()
            ?? ExpenseCategory::query()->where('is_active', true)->where('name', 'Labor')->first();
    }
}
