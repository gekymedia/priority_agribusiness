<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        
        if (!($user instanceof Employee)) {
            return redirect()->route('dashboard')
                ->with('error', 'Payslips are only available for employees.');
        }

        $sort = $request->query('sort', 'pay_period_end');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['pay_period_end', 'base_salary', 'allowances_total', 'deductions_total', 'net_pay', 'status', 'paid_at'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'pay_period_end';
        }

        $payrolls = Payroll::where('employee_id', $user->id)
            ->orderBy($sort, $direction)
            ->paginate(50)
            ->withQueryString();

        return view('payslips.index', compact('payrolls', 'sort', 'direction'));
    }

    public function show(Payroll $payroll)
    {
        $user = auth()->user();
        
        if (!($user instanceof Employee) || $payroll->employee_id !== $user->id) {
            return redirect()->route('payslips.index')
                ->with('error', 'You can only view your own payslips.');
        }

        return view('payslips.show', compact('payroll'));
    }
}
