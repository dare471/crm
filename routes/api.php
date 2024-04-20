<?php

use Illuminate\Support\Facades\Route;

require_once __DIR__.'/client/client.php';
require_once __DIR__.'/auth/auth.php';
require_once __DIR__.'/client/dashboard.php';
require_once __DIR__.'/outSideService/outSideService.php';

// Роут без применения middleware 'auth:api'
Route::post('/maps/coordinate_receive', 'App\Http\Controllers\UtilXMLController@coordinate_to_from');

