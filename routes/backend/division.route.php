<?php

Route::group(['prefix' => 'division'], function () {
    Route::get('/', 'DivisionController@index');
    Route::get('/detail/{id?}', 'DivisionController@detail');
});