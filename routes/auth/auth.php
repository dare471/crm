<?php

use Illuminate\Support\Facades\Route;

//Авторизация пользователя JWT
Route::group([
    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth',

], function () {
    
    //Авторизация Сотрудников 
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::get('profile', 'AuthController@userProfile'); 
    Route::post('refresh', 'AuthController@refresh');

    //Авторизация Клиентов 
    Route::post('login/client', 'ClientAuthController@login');
    Route::post('register/client', 'ClientAuthController@register');
    Route::post('logout/client', 'ClientAuthController@logout');
    Route::get('profile/client', 'ClientAuthController@userProfile'); 
    Route::post('refresh/client', 'ClientAuthController@refresh');
});


