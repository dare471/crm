<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

//Авторизация пользователя JWT
Route::group([

    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth',

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('profile', 'AuthController@profile');
    Route::post('refresh', 'AuthController@refresh');
});

//API-ки постраничные
Route::group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {



    //staff route
    Route::get('/staff/dashboard/revenue/{iin}', 'StaffController@staff_sales_order');
    Route::get('/staff/dashboard/count_orders/{iin}', 'StaffController@staff_sales_count_order');
    Route::get('/staff/dashboard/plan_season/{iin}', 'StaffController@staff_plan_season');
    Route::get('/staff/dashboard/mini_profile/{iin}', 'StaffController@staff_mini_p');
    Route::get('/staff/contract/{iin}', 'StaffController@staff_contracts');
    Route::get('/staff/contract/dop/count/{guid}', 'StaffController@contracts_count');
    //
   
    //coordintae to 1c 
    Route::post('/maps/coordinate_receive', 'UtilXMLController@coordinate_to_from'); 

    ///CLIENT 
    Route::get('/user_data/{id}', 'StaffController@UserSPRTable');

    
    ///test route for maps
    //Route::get('/country','StaffController@Region');
    Route::get('/region/{kat_f}','StaffController@District');
    Route::get('/elevatorMarker/', 'StaffController@ElevatorMarker');
    Route::get('/usersmigration', 'StaffController@Migrationalluser');
    //end route for maps 


    //Route v2 for Maps 
    Route::post("/country", "MapsController@MainController");
    Route::post("/filterPlots", "MapsController@MapsClient");
    Route::post("/mainquery", "MapsController@FilterForMaps");
    Route::post("/mainquery/v2", "MapsController@MapsControll");
    Route::post("/history/maps", "HistoryBrowsingController@HistoryBrowsing");
    Route::post("/analytics", "MapsAnalyticsController@AnalyticsMaps");
    Route::post("/analyse", "ClientAnalyticController@Analyse");
    Route::post("/client", "ClientAnalyticController@clientInformation");
    Route::post("/weebhook/user", "UserSettingsController@WebhookParametrs");
    Route::post("/user/info", "ProfileControtroller@ProfileClusterFunc");
    Route::post("/user/service/onec", "CServiceForOnecController@LogicForService");
    //END

    //Api for WEB v2
    Route::post("/powerbi/report", "PowerBiController@PowerBiReport"); // POWERBI 
    Route::post("/workplace", "WorkSpaceController@Worktable");
    Route::post("/comment", "FeedBackController@CommentToElement");
    Route::post("/contract", "ContractController@Contracts");
    Route::post("/manager/analyse/user", "StaffController@ManagerAnalyse");
    Route::post("/user/setting", "UserSettingsController@UserSettings");
    Route::post("/manager/workspace", "WorkSpaceController@UserPlace");
    Route::POST("/mobile", "ResponseClusterController@ResponseHeadersFunction"); //version for mobile and response cluster 
    //END
    
    //api for web
    Route::get('/contracts/{user_id}', 'StaffController@ListOrders');
    Route::get('/contracts/client/{client_id}', 'StaffController@AllOrdersClient');
    Route::get('/contracts/detail/{id}', 'StaffController@DetailOrders');
    Route::get('/contracts/adicional/{id}', 'StaffController@LinkAdicionalOrder');
    Route::get('/client_info/all/{id}', 'StaffController@Client_list');
    Route::get('/client_info/{guid}', 'StaffController@ClientInfo');
    Route::get('/client_info/managerlink/{id}', 'StaffController@ManagerClientLink');
    //END

//user controller
    Route::get('/user/{server_name}&pswd={pswd}', 'UserController@index');
    Route::get('/user/spr/{id}', 'StaffController@UserSPR');
    Route::get('/user/{id}', 'StaffController@UserProfile');

//document json body
    Route::post('/document_c/create', 'DocumentCController@create');

// journall created element
    Route::post('/journal/create', 'DocumentCController@created_j');
    Route::post('/journal/list', 'DocumentCController@list_j');
    Route::get('/journal/list_category', 'DocumentCController@list_category');
    
});
