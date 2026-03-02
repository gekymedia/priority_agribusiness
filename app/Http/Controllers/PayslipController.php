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

        $payrolls = Payroll::where('employee_id', $user->id)
            ->orderBy('pay_period_end', 'desc')
            ->paginate(15);

        return view('payslips.index', compact('payrolls'));
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
