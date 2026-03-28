<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Mail\PayrollPaidMail;
use App\Services\PayrollSalaryExpenseService;
use App\Services\Notifications\Notifier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month');
        $sort = $request->query('sort', 'pay_period');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['pay_period', 'employee', 'base_salary', 'allowances_total', 'deductions_total', 'net_pay', 'status', 'paid_at'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'pay_period';
        }

        $query = Payroll::query()->with('employee');
        if ($month) {
            $query->where('pay_period', 'like', $month . '%');
        }
        if ($sort === 'employee') {
            $query->leftJoin('employees', 'payrolls.employee_id', '=', 'employees.id')
                ->select('payrolls.*')
                ->orderBy('employees.first_name', $direction);
        } else {
            $query->orderBy('payrolls.' . $sort, $direction);
        }
        $payrolls = $query->paginate(50)->withQueryString();

        return view('payroll.index', compact('payrolls', 'month', 'sort', 'direction'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)
            ->approved()
            ->orderBy('first_name')
            ->get();
            
        return view('payroll.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_period' => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'allowances_total' => 'nullable|numeric|min:0',
            'deductions_total' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,approved,paid',
            'notes' => 'nullable|string',
        ]);

        $data['allowances_total'] = $data['allowances_total'] ?? 0;
        $data['deductions_total'] = $data['deductions_total'] ?? 0;
        $data['net_pay'] = $data['base_salary'] + $data['allowances_total'] - $data['deductions_total'];

        if ($data['status'] === 'paid') {
            $data['paid_at'] = Carbon::now();
        }

        $payroll = Payroll::create($data);

        if ($data['status'] === 'paid') {
            $this->sendPayrollNotifications($payroll);
        }

        app(PayrollSalaryExpenseService::class)->sync($payroll->fresh());

        return redirect()->route('payroll.index')->with('success', 'Payroll record created successfully.');
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::where('is_active', true)
            ->approved()
            ->orderBy('first_name')
            ->get();
            
        return view('payroll.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_period' => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'allowances_total' => 'nullable|numeric|min:0',
            'deductions_total' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,approved,paid',
            'notes' => 'nullable|string',
        ]);

        $data['allowances_total'] = $data['allowances_total'] ?? 0;
        $data['deductions_total'] = $data['deductions_total'] ?? 0;
        $data['net_pay'] = $data['base_salary'] + $data['allowances_total'] - $data['deductions_total'];

        $oldStatus = $payroll->status;
        $data['paid_at'] = $data['status'] === 'paid' ? ($payroll->paid_at ?? Carbon::now()) : null;

        $payroll->update($data);

        if ($oldStatus !== 'paid' && $data['status'] === 'paid') {
            $payroll->refresh();
            $this->sendPayrollNotifications($payroll);
        }

        app(PayrollSalaryExpenseService::class)->sync($payroll->fresh());

        return redirect()->route('payroll.index')->with('success', 'Payroll record updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        app(PayrollSalaryExpenseService::class)->detachForDeletion($payroll);
        $payroll->delete();
        return redirect()->route('payroll.index')->with('success', 'Payroll record deleted successfully.');
    }

    public function updateStatus(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,approved,paid',
        ]);

        $oldStatus = $payroll->status;
        $newStatus = $validated['status'];

        if ($newStatus === 'paid' && !$payroll->paid_at) {
            $payroll->paid_at = Carbon::now();
        }
        if ($newStatus !== 'paid') {
            $payroll->paid_at = null;
        }

        $payroll->status = $newStatus;
        $payroll->save();

        if ($oldStatus !== 'paid' && $newStatus === 'paid') {
            $this->sendPayrollNotifications($payroll);
        }

        app(PayrollSalaryExpenseService::class)->sync($payroll->fresh());

        return response()->json([
            'status' => $payroll->status,
            'status_label' => ucfirst($payroll->status),
            'paid_at' => $payroll->paid_at ? $payroll->paid_at->format('Y-m-d H:i') : null,
        ]);
    }

    protected function sendPayrollNotifications(Payroll $payroll): void
    {
        $employee = $payroll->employee;
        if (!$employee) {
            return;
        }

        $period = Carbon::parse($payroll->pay_period)->format('M Y');
        $netPay = number_format((float) $payroll->net_pay, 2);
        $appName = config('app.name');

        // Email notification
        if (!empty($employee->email)) {
            try {
                Mail::to($employee->email)->send(new PayrollPaidMail($payroll));
                Log::info('Payroll paid email sent', ['employee_id' => $employee->id, 'payroll_id' => $payroll->id]);
            } catch (\Throwable $e) {
                Log::warning('Payroll paid mail failed: ' . $e->getMessage());
            }
        }

        // SMS notification
        if (!empty($employee->phone)) {
            try {
                $notifier = app(Notifier::class);
                $smsMsg = "Hi {$employee->full_name}, your salary for {$period} has been PAID. Net: GHS {$netPay}. - {$appName}";
                $notifier->sms($employee->phone, $smsMsg);
                Log::info('Payroll paid SMS sent', ['employee_id' => $employee->id, 'payroll_id' => $payroll->id]);
            } catch (\Throwable $e) {
                Log::warning('Payroll paid SMS failed: ' . $e->getMessage());
            }
        }

        // GekyChat notification
        if (!empty($employee->phone)) {
            try {
                $notifier = app(Notifier::class);
                $gekyChatMsg = "Hi {$employee->full_name},\n\nYour salary for {$period} has been PAID.\nNet Amount: GHS {$netPay}\n\nThank you!\n- {$appName}";
                $notifier->gekychat($employee->phone, $gekyChatMsg);
                Log::info('Payroll paid GekyChat sent', ['employee_id' => $employee->id, 'payroll_id' => $payroll->id]);
            } catch (\Throwable $e) {
                Log::warning('Payroll paid GekyChat failed: ' . $e->getMessage());
            }
        }
    }
}
