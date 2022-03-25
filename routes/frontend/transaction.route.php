<?php

Route::group(['middleware' => 'checkLogin'], function () {
    Route::group(['prefix' => 'transaction'], function () {
        Route::get('/', 'TransactionController@index');
        Route::post('/', 'TransactionController@store');
        Route::get('/create', 'TransactionController@create');
        Route::get('/edit/{id?}', 'TransactionController@edit');
        Route::post('/update/{id?}', 'TransactionController@update');
        Route::get('/delete/{id?}', 'TransactionController@delete');
        Route::get('/delete-transaction/{id?}', 'TransactionController@deleteTransaction');
        Route::get('/finance-report', 'TransactionController@financeReport');
        Route::get('/finance-report-xlsx', 'TransactionController@exportFinanceReport');
        Route::post('/get-extra-info', 'TransactionController@countTransaction');
        Route::get('/{id}', 'TransactionController@view');
        Route::get('/detail/{id?}', 'TransactionController@detail');
        Route::get('/push-payment/{id?}', 'TransactionController@pushPayment');
        Route::get('/push-manual-journal/{id?}', 'TransactionController@pushManualJournal');
        Route::get('/get-saldo-flip/{key}', 'TransactionController@getSaldo');
        Route::group(['prefix' => 'chart-of-accounts'], function () {
            Route::get("/get/{id?}", 'TransactionController@getChartOfAccounts');
            Route::post("/change/", 'TransactionController@changeChartOfAccounts');
        });

        Route::group(['prefix' => 'approve'], function () {
            Route::get('/user/{id?}', 'TransactionController@approveUser');
            Route::get('/director/{id?}', 'TransactionController@approveDirector');
            Route::get('/finance/{id?}', 'TransactionController@approveFinance');
        });

        Route::group(['prefix' => 'reject'], function () {
            Route::post('/director/{id?}', 'TransactionController@rejectDirector');
            Route::post('/finance/{id?}', 'TransactionController@rejectFinance');
            Route::post('/master-finance/{id?}', 'TransactionController@rejectMasterFinance');
        });

        Route::post('upload-file', 'TransactionController@uploadFile');
        Route::post('/finance-note', 'TransactionController@financeNoteStoreOrEdit');
    });
});