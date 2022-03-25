<?php


Route::middleware(['checkAdmin'])->group(function () {
    Route::get('/', function () {
        return redirect(url('backend/dashboard'));
    });
    Route::get('/dashboard', 'IndexController@dashboard'); 
});
Route::get('/login', 'AuthLoginController@login');
Route::post('/login', 'AuthLoginController@loginPost');
Route::post('/logout', 'AuthLoginController@logout');