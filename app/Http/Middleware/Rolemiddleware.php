<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Rolemiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    {
        // dd($roles);
        if (!auth()->check()) {
            abort(403);
        }

        if (!in_array(auth()->user()->role, $roles) || auth()->user()->status!=1) {
            Auth::logout();
            abort(403, 'Unauthorized');
        }

        // return $next($request);
        return $next($request);
    }
}
