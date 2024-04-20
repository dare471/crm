<?php

use Illuminate\Support\Facades\Route;

//Авторизация пользователя JWT
Route::group([
    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth',

], function () {
    Route::post('login', 'AuthController@login');
    Route::post('login/client', 'client\ClientAuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('register/client', 'client\ClientAuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('logout/client', 'client\ClientAuthController@logout');
    Route::get('profile', 'AuthController@userProfile'); 
    Route::get('profile/client', 'client\ClientAuthController@userProfile'); 
    Route::post('refresh', 'AuthController@refresh');
    Route::post('refresh/client', 'client\ClientAuthController@refresh');
});


