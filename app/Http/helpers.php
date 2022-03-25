<?php

if(! function_exists('alertNotify')){
    function alertNotify($isSuccess  = true, $message = '', $request){
        if($isSuccess){
            $request->session()->flash('alert-class','bg-green-50');
            $request->session()->flash('status', $message);
        }else{
            $request->session()->flash('alert-class',' bg-red-50');
            $request->session()->flash('status', $message);
        }
    }
}

if(! function_exists('returnCustom')){
    function returnCustom($message, $status = false, $log = false){
        if ($log) {
            \Illuminate\Support\Facades\Log::error($message);
        }
        return ['status' => $status, 'message' => $message];
    }
}

if(! function_exists('memberOrderPackages')){
    function memberOrderPackages(){
        $list = [
            [
                'name' => 'Basic',
                'price' => 7500000,
                'long_expired' => '1 year'
            ]
        ];
        return $list;
    }
}

/* check if contain user role */
if(! function_exists('hasRole')){
    /* $roleName string type with role name */
    function hasRole($roleName){
        try {
            /* decode from base64 encode cookie into user object */
            $userData = json_decode(base64_decode($_COOKIE['__agl']));
            foreach ($userData->roles as $role) {
                /* if found role return true */
                if ($role->slug == $roleName){
                    return true;
                }
            }
            return false;
        }catch (\Exception $e){
            return false;
        }
    }
}

if(! function_exists('adminUser')){
    function adminUser(){
        try {
            return json_decode(base64_decode($_COOKIE['__agl']));
        }catch (\Exception $e){
            return null;
        }
    }
}