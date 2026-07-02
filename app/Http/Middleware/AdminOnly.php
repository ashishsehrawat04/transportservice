<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('login')->with('success', 'Please login as admin to access that page.');
        }

        return $next($request);
    }
}
