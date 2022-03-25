<?php

Route::group(['middleware' => 'checkNotLogin'], function () {
    Route::group(['prefix' => 'register'], function () {
        Route::get('/', 'AuthController@register');
        Route::post('/', 'AuthController@registerPost');
    });

    Route::group(['prefix' => 'login'], function() {
        Route::get('/', 'AuthController@login');
        Route::post('/', 'AuthController@loginPost');
        Route::get('/otp', 'AuthController@otpverification');
        Route::post('/otp', 'AuthController@postOtpVerificationLogin');
        Route::get('/get-company-name', 'AuthController@getCompanyName');
    });
});