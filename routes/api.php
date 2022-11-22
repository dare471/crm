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

    //ELevator Work table 
    Route::get('/elevator/table', 'StaffController@WorkTable'); //list all elements
    Route::get('/elevator/element/{id}', 'StaffController@ElementElevator'); //one elements 
    Route::post('/elevator/element/add', 'StaffController@ElementElevatorAdd');
    Route::post('/elevator/table/update', 'StaffController@UpdateDataTable'); // post query for update data
    Route::post('/elevator/tablevs/update', 'StaffController@UpdateDataTableSV'); // post query for update data
    Route::get('/elevator/element/delete/{id}', 'StaffController@ElementDelete');
    
    
    
    
    ///test route for maps
    Route::get('/country','StaffController@Region');
    Route::get('/district/{cato}','StaffController@PolygonCLient');
    Route::get('/region/{kat_f}','StaffController@District');
    Route::get('/district_new/{cato}', 'StaffController@ClientFields');
    Route::get('/elevatorMarker/', 'StaffController@ElevatorMarker');
    Route::post('/clientDistrictFields/','StaffController@clientDistrictFields'); /// ----
    Route::get('/getCultureSpr/{region}', 'StaffController@GetCultureSpr');
    Route::post('/filterFields','StaffController@FilterClientFields');
    // // Route::get('/clientPolygons/{guid}', 'StaffController@ClientGuid');
    Route::post('/clientFieldsCult', 'StaffController@ClientGroupCulture');//---
    Route::post('/getClientFieldsCult', 'StaffController@ClientFieldGuid'); ///----
    Route::get('/clientPolygon/{id}', 'StaffController@PolygonDetail');
    Route::get('/clientField/{id}', 'StaffController@FieldsDetail'); /// ----
    Route::get('/usersmigration', 'StaffController@Migrationalluser');
    //end route for maps 

    // Route FOR TESTING !!!
    Route::get('/testdo/{id}', 'StaffController@todol');
    Route::post('/testdo/v2/', 'StaffController@TestClientFields');
    //END

    //Route v2 for Maps 
    Route::post("/country/v2/", "MapsController@MainController");
    Route::post("/clientFields/v2/", "MapsController@MapsClient");
    Route::post("/filter", "MapsController@FilterForMaps");
    Route::post("/history/maps", "HistoryBrowsingController@HistoryBrowsing");
    Route::post("/analytics", "MapsAnalyticsController@AnalyticsMaps");
    //END

    //Api for WEB v2
    Route::post("/contracts/v2", "ContractController@Contracts"); //Contracts manager with client
    Route::post("/powerbi/report", "PowerBiController@PowerBiReport"); // POWERBI 
    Route::post("/workplace", "WorkSpaceController@Worktable");
    //END
    
    //api for web
    Route::get('/contracts/{user_id}', 'StaffController@ListOrders');
    Route::get('/contracts/client/{client_id}', 'StaffController@AllOrdersClient');
    Route::get('/contracts/detail/{id}', 'StaffController@DetailOrders');
    Route::get('/contracts/adicional/{id}', 'StaffController@LinkAdicionalOrder');
    ROute::get('/findbyiin/{iin}', 'StaffController@FindClientName');
    Route::get('/client_info/all/{id}', 'StaffController@Client_list');
    Route::get('/client_info/{guid}', 'StaffController@ClientInfo');
    Route::get('/client_info/managerlink/{id}', 'StaffController@ManagerClientLink');
    Route::post('/comment/add', 'StaffController@AddCommentContent');
    Route::post('/comment/update', 'StaffController@UpdateCommentContent');
    Route::post('/comment/delete', 'StaffController@DeleteCommentContent');
    Route::post('/comment/list', 'StaffController@ListCommentContent');
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
