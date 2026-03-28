<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('X-API-TOKEN');

        if (!$token) {
            return response()->json([
                'error'   => 'Unauthenticated',
                'message' => 'Provide your API token via Authorization: Bearer {token} header.',
            ], 401);
        }

        $user = User::where('api_code', $token)
            ->whereIn('role', ['hospital_admin', 'super_admin'])
            ->where('status', 1)
            ->first();

        if (!$user) {
            return response()->json([
                'error'   => 'Invalid token',
                'message' => 'The API token is invalid or the account is inactive.',
            ], 401);
        }

        // Make user available to the request
        $request->merge(['_api_user' => $user]);

        return $next($request);
    }
}