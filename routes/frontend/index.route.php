<?php

Route::get('/', function () {
    return redirect(url('/login'));
});

Route::group(['middleware' => 'checkLogin'], function () {
    Route::get('/dashboard', 'IndexController@dashboard');
});

Route::group(['middleware' => 'checkOnlyLogin'], function () {
    Route::get('/payment/{invoice_number?}', 'IndexController@payment');
    Route::get('logout', 'AuthController@logout');
    Route::get('cancel-option/{id}', 'IndexController@cancelOption');
    Route::get('profile', 'IndexController@profile');
    Route::get('change-password', 'IndexController@changePassword');
    Route::post('change-password', 'IndexController@changePasswordProcess');
});

Route::get('forgot-password', 'IndexController@forgotPassword');
Route::post('forgot-password', 'IndexController@forgotPasswordProcess');
Route::get('request-password', 'IndexController@requestPassword');
Route::post('request-password', 'IndexController@requestPasswordProcess');

Route::get('error-page', function () {
    $message = \Illuminate\Support\Facades\Session::get('status');
    return view('frontend.index.error-page', compact('message'));
});