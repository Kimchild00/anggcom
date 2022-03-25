<?php

Route::group(['prefix' => 'inquiry'], function () {
    Route::get('/', 'InquiryController@index');
});