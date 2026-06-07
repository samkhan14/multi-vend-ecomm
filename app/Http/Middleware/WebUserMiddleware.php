<?php
// app/Http/Middleware/WebUserMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebUserMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->user_type == 'webuser' && Auth::user()->user_status == 1) {
            return $next($request);
        }
        
        return redirect()->route('user.login')->with('error', 'Please login to access this page.');
    }
}