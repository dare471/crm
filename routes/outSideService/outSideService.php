<?php 
use Illuminate\Support\Facades\Route;
//API-ки постраничные
Route::group([
    'middleware' => ['api', 'auth:api'],
    'namespace' => 'App\Http\Controllers\user',
], function () {
    Route::post("/user/dashboard", "DashboardController@RouteCase");
});
