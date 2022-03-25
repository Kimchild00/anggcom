<?php

Route::group(['middleware' => 'checkLogin'], function () {
    Route::group(['prefix' => 'user'], function () {
        Route::get('/', 'UserController@index');
        Route::get('/create', 'UserController@create');
        Route::put('/update-otp/{id?}', 'UserController@updateOtp');
        Route::post('/reset-password/{id?}', 'UserController@resetPasswordProcess');
        Route::post('/', 'UserController@store');
        Route::get('/edit/{id?}', 'UserController@edit');
        Route::put('/{id}', 'UserController@update');
        Route::get('/delete/{id?}', 'UserController@delete');
    });
});