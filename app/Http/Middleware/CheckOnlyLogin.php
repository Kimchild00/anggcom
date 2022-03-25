<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckOnlyLogin
{
    public function handle($request, Closure $next)
    {
        // Check user has logged in, if not redirect to login page
        $isLogin    = Auth::check();
        if (!$isLogin) {
            return redirect(url('login'));
        }
        return $next($request);
    }
}