<?php

Route::group(['prefix' => 'user-company'], function () {
    Route::get('/', 'UserCompanyController@index');
});