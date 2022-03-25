<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Hash;

class AdminAuthorization
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
        /*$userData = adminUser();
        if (isset($userData->key, $_COOKIE['__agl'], $userData->roles)){
            if (Hash::check(config('app.key'), $userData->key) && hasRole('anggaran-login')) {*/
                return $next($request);
        /*    }
        }
        alertNotify(false, "Please logged in by Backend COM", $request);
        return redirect(url('/'));*/
    }
}
