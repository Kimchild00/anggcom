<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Hash;

class AuthLoginController extends Controller
{
    public function login() {
        if(\Sentinel::check()) {
            return redirect(url('backend/dashboard'));
        }
        return view("backend.login.index");
    }

    public function loginPost(Request $request) {
        $validator = \Validator::make($request->all(), [
            "email"     => "required",
            "password"  => "required"
        ]);

        if($validator->fails()) {
            return redirect(url("/backend/login"))  
            ->withErrors($validator->errors())
            ->withInput($request->all());
        }

        $response = (new \App\Repositories\AuthBackendRepository())->loginPost($request->get('email'), $request->get('password'));
        if(!$response['status']) {
            return redirect(url("/backend/login"))  
                ->withErrors($response['message'])
                ->withInput($request->all());
        }
    
        return redirect(url("backend/dashboard"));
    }

    public function logout(){
        Sentinel::logout();
        return redirect(url('/backend'));
    }
}
