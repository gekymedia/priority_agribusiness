<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Farm;
use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = Employee::with(['farm', 'house'])->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")->orderBy('created_at', 'desc');

        if ($request->filled('status_filter')) {
            if ($request->status_filter === 'pending') {
                $query->pending();
            } elseif ($request->status_filter === 'approved') {
                $query->approved();
            }
        }

        $employees = $query->paginate(15)->withQueryString();
        $pendingCount = Employee::pending()->count();

        return view('employees.index', compact('employees', 'pendingCount'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $farms = Farm::all();
        $houses = House::with('farm')->get();
        
        return view('employees.create', compact('farms', 'houses'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'access_level' => 'required|in:admin,manager,caretaker,viewer',
            'farm_id' => 'nullable|exists:farms,id',
            'house_id' => 'nullable|exists:houses,id',
            'hire_date' => 'nullable|date',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Generate unique employee ID; new employees start as pending until admin approves
        $data['employee_id'] = 'EMP-' . strtoupper(Str::random(8));
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;
        $data['status'] = 'pending';

        Employee::create($data);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created. They are pending approval and cannot log in until an admin approves them.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        $employee->load(['farm', 'house', 'tasks']);
        
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $farms = Farm::all();
        $houses = House::with('farm')->get();
        
        return view('employees.edit', compact('employee', 'farms', 'houses'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'access_level' => 'required|in:admin,manager,caretaker,viewer',
            'farm_id' => 'nullable|exists:farms,id',
            'house_id' => 'nullable|exists:houses,id',
            'hire_date' => 'nullable|date',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'status' => 'required|in:pending,approved',
            'password' => 'nullable|string|min:8|confirmed',
            'base_salary' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
        ]);

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Process allowances from form arrays
        $allowanceNames = $request->input('allowance_names', []);
        $allowanceAmounts = $request->input('allowance_amounts', []);
        $allowances = [];
        
        foreach ($allowanceNames as $index => $name) {
            $name = trim($name);
            $amount = floatval($allowanceAmounts[$index] ?? 0);
            if (!empty($name) && $amount > 0) {
                $allowances[$name] = $amount;
            }
        }
        $data['allowances'] = !empty($allowances) ? $allowances : null;

        $employee->update($data);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Approve a pending employee so they can log in and use the system.
     */
    public function approve(Employee $employee)
    {
        if ($employee->status === 'approved') {
            return redirect()->route('employees.index')
                ->with('info', 'Employee is already approved.');
        }

        $employee->update(['status' => 'approved']);

        return redirect()->route('employees.index')
            ->with('success', $employee->full_name . ' has been approved and can now log in.');
    }
}
