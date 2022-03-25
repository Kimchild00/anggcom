<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

class ImportirAuthController extends Controller
{
    public function auth(Request $request)
    {
        if($request->token){
            setcookie('__agl', $request->token, time()+1800, '/', NULL, 0, 1);
        }
        return view('api.admin-auth', ['token' => $request->token]);
    }
}
