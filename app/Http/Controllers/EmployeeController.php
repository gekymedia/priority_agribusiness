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
    public function index()
    {
        $employees = Employee::with(['farm', 'house'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('employees.index', compact('employees'));
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

        // Generate unique employee ID
        $data['employee_id'] = 'EMP-' . strtoupper(Str::random(8));
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        Employee::create($data);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
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
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

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
}
