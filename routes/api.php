<?php

//Public Routes

// Route grop for authenticated users only
Route::group(['middleware' => ['auth:api']], function () {

});

// Route grop for guest users only
Route::group(['middleware' => ['guest:api']], function () {
    Route::post('register', 'Auth\RegisterController@register');
});
