<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckNotLogin
{
    public function handle($request, Closure $next)
    {
        // Check user has not logged in, if login redirect to dashboard
        $isLogin    = Auth::check();
        if ($isLogin) {
            return redirect(url('dashboard'));
        }
        return $next($request);
    }
}