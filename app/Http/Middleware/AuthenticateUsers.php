<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateUsers
{
    /**
     * Handle an incoming request.
     * Checks both 'web' (User) and 'employee' guards for authentication.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via either guard
        if (Auth::guard('web')->check() || Auth::guard('employee')->check()) {
            return $next($request);
        }

        // If not authenticated, redirect to login
        return redirect()->route('login');
    }
}
