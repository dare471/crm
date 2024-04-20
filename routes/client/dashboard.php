<?php 

use Illuminate\Support\Facades\Route;
//API-ки постраничные
Route::group([
    'middleware' => ['api', 'auth:api'],
    'namespace' => 'App\Http\Controllers\client',
], function () {
    Route::prefix('/user')->group(function () {
        Route::post('/dashboard', 'DashboardController@RouteCase');
    });
});
