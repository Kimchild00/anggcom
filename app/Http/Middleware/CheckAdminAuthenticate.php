<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(\Sentinel::check()) {
            return $next($request);
        }
        return redirect(url("/backend/login"));
    }
}
