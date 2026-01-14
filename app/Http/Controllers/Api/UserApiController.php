<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserApiController extends Controller
{
    /**
     * Get authenticated user's profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'business_info' => [
                    'total_farms' => $user->farms()->count(),
                    'total_houses' => $user->houses()->count(),
                    'total_fields' => $user->fields()->count(),
                ]
            ]
        ]);
    }

    /**
     * Get user's business information
     */
    public function businessInfo(Request $request)
    {
        $user = $request->user();

        $farms = $user->farms()->with(['houses', 'fields'])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'farms' => $farms->map(function ($farm) {
                    return [
                        'id' => $farm->id,
                        'name' => $farm->name,
                        'location' => $farm->location,
                        'type' => $farm->farm_type,
                        'houses_count' => $farm->houses->count(),
                        'fields_count' => $farm->fields->count(),
                    ];
                }),
                'summary' => [
                    'total_farms' => $farms->count(),
                    'total_houses' => $farms->sum(function ($farm) { return $farm->houses->count(); }),
                    'total_fields' => $farms->sum(function ($farm) { return $farm->fields->count(); }),
                ]
            ]
        ]);
    }

    /**
     * Generate API token for external access
     */
    public function generateToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'sometimes|string|max:255'
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($request->device_name ?: 'API Access')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]
        ]);
    }
}
