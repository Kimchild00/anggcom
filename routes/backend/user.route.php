<?php

Route::group(['prefix' => 'user'], function () {
    Route::get('/', 'UserController@index');
    Route::get('/detail/{id?}', 'UserController@detail');
    Route::get('/paid-manually/{id?}', 'UserController@paidManually');
});