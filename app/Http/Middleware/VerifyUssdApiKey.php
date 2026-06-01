<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyUssdApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.ussd.api_key');
        if (empty($expected)) {
            return response()->json([
                'success' => false,
                'message' => 'USSD API is not configured.',
            ], 503);
        }

        $provided = $request->header('X-USSD-API-Key')
            ?? $request->header('Authorization');

        if (is_string($provided) && str_starts_with($provided, 'Bearer ')) {
            $provided = substr($provided, 7);
        }

        if (! is_string($provided) || ! hash_equals($expected, $provided)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        return $next($request);
    }
}
