<?php

Route::group(['middleware' => 'checkLogin'], function () {
    Route::group(['prefix' => 'division'], function () {
        Route::get('/', 'DivisionController@index');
        Route::post('/', 'DivisionController@store');
        Route::get('/create', 'DivisionController@create');
        Route::get('/edit/{id?}', 'DivisionController@edit');
        Route::post('/update/', 'DivisionController@updatePost');
        Route::get('/delete/{id?}', 'DivisionController@delete');
        Route::get('/{id}', 'DivisionController@view');
        Route::post('/create-user', 'DivisionController@createUser');
        Route::get('/delete-user/{id?}/{userId?}/{role?}', 'DivisionController@deleteUser');
    });
});