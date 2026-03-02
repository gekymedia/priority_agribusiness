<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CrudNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,poultry_manager,crop_manager',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('employees.index', ['type_filter' => 'users'])
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $currentUser = auth()->user();
        
        if ($currentUser instanceof User && $currentUser->id === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('employees.index', ['type_filter' => 'users'])
            ->with('success', 'User deleted successfully.');
    }
}
