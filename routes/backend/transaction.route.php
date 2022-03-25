<?php

Route::group(['prefix' => 'transaction'], function () {
    Route::get('/', 'TransactionController@index');
    Route::get('/detail/{id?}', 'TransactionController@detail');
});