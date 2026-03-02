<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Farm;
use App\Models\House;
use App\Models\User;
use App\Services\CrudNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees and users.
     */
    public function index(Request $request)
    {
        $typeFilter = $request->get('type_filter', 'all');
        $statusFilter = $request->get('status_filter');

        $employees = collect();
        $users = collect();

        if ($typeFilter === 'all' || $typeFilter === 'employees') {
            $empQuery = Employee::with(['farm', 'house'])
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderBy('created_at', 'desc');

            if ($statusFilter === 'pending') {
                $empQuery->pending();
            } elseif ($statusFilter === 'approved') {
                $empQuery->approved();
            }

            $employees = $empQuery->get()->map(function ($emp) {
                $emp->record_type = 'employee';
                $emp->display_name = $emp->full_name;
                $emp->display_role = $emp->access_level;
                return $emp;
            });
        }

        if ($typeFilter === 'all' || $typeFilter === 'users') {
            $users = User::orderBy('created_at', 'desc')->get()->map(function ($user) {
                $user->record_type = 'user';
                $user->display_name = $user->name;
                $user->display_role = $user->role ?? 'user';
                $user->status = 'approved';
                $user->is_active = true;
                return $user;
            });
        }

        $combined = $employees->merge($users)->sortByDesc('created_at');
        
        $perPage = 15;
        $page = $request->get('page', 1);
        $total = $combined->count();
        $items = $combined->forPage($page, $perPage)->values();
        
        $records = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $pendingCount = Employee::pending()->count();
        $employeeCount = Employee::count();
        $userCount = User::count();

        return view('employees.index', compact('records', 'pendingCount', 'employeeCount', 'userCount', 'typeFilter', 'statusFilter'));
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

        $employee = Employee::create($data);

        app(CrudNotificationService::class)->notify('employees', 'created', $employee, auth()->user());

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

        app(CrudNotificationService::class)->notify('employees', 'updated', $employee, auth()->user());

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        $employeeCopy = clone $employee;
        $employee->delete();

        app(CrudNotificationService::class)->notify('employees', 'deleted', $employeeCopy, auth()->user());

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
