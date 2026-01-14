<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $requiredLevel  The minimum access level required
     */
    public function handle(Request $request, Closure $next, string $requiredLevel = 'viewer'): Response
    {
        $user = Auth::user();
        
        // If user is a regular User (not Employee), check their role
        if ($user instanceof \App\Models\User) {
            // Regular users (farm owners/admins) have full access to all features
            // They can manage employees regardless of their role field
            return $next($request);
        }
        
        // If user is an Employee, check their access level
        if ($user instanceof \App\Models\Employee) {
            if (!$user->is_active) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Your account has been deactivated.');
            }
            
            if ($user->hasAccessLevel($requiredLevel)) {
                return $next($request);
            }
            
            abort(403, 'Insufficient access level.');
        }
        
        // If not authenticated, redirect to login
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        abort(403, 'Access denied.');
    }
}
