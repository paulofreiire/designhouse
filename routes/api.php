<?php

//Public Routes
Route::get('me', 'User\MeController@getMe');

//Get designs
Route::get('designs', 'Designs\DesignController@index');
Route::get('designs/{id}', 'Designs\DesignController@show');

//Get Users
Route::get('users', 'User\UserController@index');

Route::get('teams/{id}', 'Team\TeamsController@findBySlug');

// Route grop for authenticated users only
Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', 'Auth\LoginController@logout');
    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password', 'User\SettingsController@updatePassword');

    //Upload Designs
    Route::post('designs', 'Designs\UploadController@upload');
    Route::put('designs/{id}', 'Designs\DesignController@update');
    Route::delete('designs/{id}', 'Designs\DesignController@destroy');

    //Comments
    Route::post('designs/{id}/comments', 'Designs\CommentController@store');
    Route::put('comments/{id}', 'Designs\CommentController@update');
    Route::delete('comments/{id}', 'Designs\CommentController@destroy');

    //Likes
    Route::post('designs/{id}/like', 'Designs\DesignController@like');
    Route::get('designs/{id}/liked', 'Designs\DesignController@checkIfUserHasLiked');

    //Team
    Route::get('teams/{id}', 'Teams\TeamController@findById');
    Route::post('teams', 'Teams\TeamController@store');
    Route::get('teams', 'Teams\TeamController@index');
    Route::get('users/teams', 'Teams\TeamController@fetchUserTeams');
    Route::put('teams/{id}', 'Teams\TeamController@update');
    Route::delete('teams/{id}', 'Teams\TeamController@destroy');


});

// Route grop for guest users only
Route::group(['middleware' => ['guest:api']], function () {
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
});




