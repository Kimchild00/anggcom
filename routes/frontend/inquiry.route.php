<?php

Route::group(['middleware' => 'checkLogin'], function () {
    Route::group(['prefix' => 'inquiry'], function () {
        Route::get('/', 'InquiryController@index');
        Route::get('/create', 'InquiryController@create');
        Route::post('/', 'InquiryController@store');
        Route::get('/update/{id?}', 'InquiryController@update');
        Route::post('/update', 'InquiryController@updatePost');
        Route::get('/delete/{id?}', 'InquiryController@delete');
    });
});