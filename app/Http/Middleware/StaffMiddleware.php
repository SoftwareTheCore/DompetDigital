<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'bank mini') {
            return $next($request); // Allow access if role is 'bank mini'
        }

        // Redirect jika user tidak memiliki role 'bank mini'
        return redirect('/')->with('error', 'You are not authorized to access this page.');
    }
}
