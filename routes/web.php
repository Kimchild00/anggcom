<?php


Route::group(['namespace' => 'Frontend'], function () {
    foreach (glob("../routes/frontend/*.route.php") as $filename) {
        include $filename;
    }
});

Route::get('admin', function () {
    return redirect('backend/dashboard');
});


Route::group(['prefix' => 'backend', 'namespace' => 'Backend', 'middleware' => 'adminAuth'], function () {
    foreach (glob("../routes/backend/*.route.php") as $filename) {
        include $filename;
    }
});