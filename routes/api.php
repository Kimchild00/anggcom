<?php

Route::group(['prefix' => 'flip', 'namespace' => 'Api'], function () {
    Route::post('/callback/{id?}', 'FlipController@callback');
    Route::get('/create-pwf/{token?}', 'FlipController@createPwf');
});

Route::group(['prefix' => 'midtrans', 'namespace' => 'Api'], function () {
    Route::post('/checkout', 'MidtransController@checkout');
    Route::any('/callback', 'MidtransController@callback');
    Route::post('/save-info/{id?}', 'MidtransController@saveInfo');
});

Route::get('/importir-authorize', 'Api\ImportirAuthController@auth');
Route::get('add-company-com/{id?}/{token?}', 'Api\ApiController@addCompanyCom');
Route::get('/push-manual-journal/{id?}', 'Api\ApiController@pushManualJournal');
Route::get('/list-coa-by-division/{id?}', 'Api\ApiController@listCoaByDivision');

Route::group(['prefix' => 'xendit', 'namespace' => 'Api'], function () {
    Route::any('/disbursement-callback', 'XenditController@disbursementCallback');
    Route::any('/disbursement-create', 'XenditController@disbursementCreate');
});