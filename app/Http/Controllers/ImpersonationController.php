<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonationController extends Controller
{
    public function start(Request $request, Employee $employee)
    {
        $currentUser = auth()->user();
        
        if (!$this->canImpersonate($currentUser)) {
            return redirect()->back()->with('error', 'You do not have permission to impersonate users.');
        }

        if ($employee->id === $currentUser->id && $currentUser instanceof Employee) {
            return redirect()->back()->with('error', 'You cannot impersonate yourself.');
        }

        Session::put('impersonator_id', $currentUser->id);
        Session::put('impersonator_type', get_class($currentUser));
        Session::put('impersonation_started_at', now());

        Auth::guard('web')->logout();
        Auth::guard('employee')->login($employee);

        return redirect()->route('dashboard')->with('success', "You are now impersonating {$employee->full_name}.");
    }

    public function startUser(Request $request, User $user)
    {
        $currentUser = auth()->user();
        
        if (!$this->canImpersonate($currentUser)) {
            return redirect()->back()->with('error', 'You do not have permission to impersonate users.');
        }

        if ($user->id === $currentUser->id && $currentUser instanceof User) {
            return redirect()->back()->with('error', 'You cannot impersonate yourself.');
        }

        Session::put('impersonator_id', $currentUser->id);
        Session::put('impersonator_type', get_class($currentUser));
        Session::put('impersonation_started_at', now());

        Auth::guard('employee')->logout();
        Auth::guard('web')->login($user);

        return redirect()->route('dashboard')->with('success', "You are now impersonating {$user->name}.");
    }

    public function stop(Request $request)
    {
        $impersonatorId = Session::get('impersonator_id');
        $impersonatorType = Session::get('impersonator_type');

        if (!$impersonatorId || !$impersonatorType) {
            return redirect()->route('dashboard')->with('error', 'No active impersonation session.');
        }

        Auth::guard('employee')->logout();

        if ($impersonatorType === User::class) {
            $user = User::find($impersonatorId);
            if ($user) {
                Auth::guard('web')->login($user);
            }
        } elseif ($impersonatorType === Employee::class) {
            $employee = Employee::find($impersonatorId);
            if ($employee) {
                Auth::guard('employee')->login($employee);
            }
        }

        Session::forget(['impersonator_id', 'impersonator_type', 'impersonation_started_at']);

        return redirect()->route('employees.index')->with('success', 'Impersonation ended.');
    }

    protected function canImpersonate($user): bool
    {
        if ($user instanceof User) {
            return true;
        }

        if ($user instanceof Employee) {
            return $user->isAdmin();
        }

        return false;
    }

    public static function isImpersonating(): bool
    {
        return Session::has('impersonator_id');
    }

    public static function getImpersonator()
    {
        $impersonatorId = Session::get('impersonator_id');
        $impersonatorType = Session::get('impersonator_type');

        if (!$impersonatorId || !$impersonatorType) {
            return null;
        }

        return $impersonatorType::find($impersonatorId);
    }
}
