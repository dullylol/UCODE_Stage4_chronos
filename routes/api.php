<?php

use Illuminate\Support\Facades\Route;

// Authentification
Route::post('/register', 'App\Http\Controllers\UserController@register');
Route::post('/authenticate', 'App\Http\Controllers\UserController@authenticate');
Route::get('/authenticated-user', 'App\Http\Controllers\UserController@getAuthenticatedUser');
Route::post('/logout', 'App\Http\Controllers\UserController@logout');
//Route::get('/refresh', 'App\Http\Controllers\AuthController@refresh');
//Route::post('/password-reset', 'App\Http\Controllers\UsersController@sendToEmail');
//Route::post('/password-reset/{confirm_token}', 'App\Http\Controllers\UsersController@resetPasswordWithToken');

// Events
Route::prefix('events')->group(function () {
    Route::get('', 'App\Http\Controllers\EventController@all');
    Route::post('', 'App\Http\Controllers\EventController@create');
    Route::get('/{event_id}', 'App\Http\Controllers\EventController@byId');
    Route::patch('/{event_id}', 'App\Http\Controllers\EventController@update');
    Route::delete('/{event_id}', 'App\Http\Controllers\EventController@destroy');
});

// Users
Route::prefix('users')->group(function () {
    Route::get('', 'App\Http\Controllers\UserController@all');
    Route::post('', 'App\Http\Controllers\UserController@create');
    Route::get('/{user_id}', 'App\Http\Controllers\UserController@byId');
    Route::patch('/{user_id}', 'App\Http\Controllers\UserController@update');
    Route::delete('/{user_id}', 'App\Http\Controllers\UserController@destroy');
});

// Groups
Route::prefix('groups')->group(function () {
    Route::get('', 'App\Http\Controllers\GroupController@all');
    Route::post('', 'App\Http\Controllers\GroupController@create');
    Route::get('/{group_id}', 'App\Http\Controllers\GroupController@byId');
    Route::patch('/{group_id}', 'App\Http\Controllers\GroupController@update');
    Route::delete('/{group_id}', 'App\Http\Controllers\GroupController@destroy');
});

// Password reset
Route::prefix('password')->group(function () {
    Route::post('/reset', 'App\Http\Controllers\PasswordResetController@ForgotPassword');
    Route::post('/reset/{token}', 'App\Http\Controllers\PasswordResetController@ResetPassword');
    Route::get('/reset/{token}/remove', 'App\Http\Controllers\PasswordResetController@RemoveRequestPassword');
});