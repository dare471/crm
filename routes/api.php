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

    //POWER BI REPORTS POINT 
    Route::get('/report/all', 'AnalysesController@report_all');
    Route::post('/report/insert', 'AnalysesController@report_insert');
    Route::post('/report/update', 'AnalysesController@report_update');
    Route::post('/report/delete', 'AnalysesController@report_delete');
    Route::get('/report/{id}', 'AnalysesController@report_detail');
    Route::get('/analytics', 'AnalysesController@analytics');
    Route::get('/topmanager', 'AnalysesController@topmanager');
    //POWERBi REPORTS END POINT

    //callculator
    Route::get('/product_own', 'ProductOwnController@all');
    Route::post('/product_own/update/{id}', 'ProductOwnController@update');

    Route::get('calculator_kp', 'CalculatorKPController@all');
    Route::get('/calculator_kp/cult', 'CalculatorKPController@cult_all');
    Route::get('/calculator_kp/pest_t', 'CalculatorKPController@pesticides_types');
    Route::get('/calculator_kp/pest_t', 'CalculatorKPController@pesticides_types');
    Route::get('/calculator_kp/pest_f/{id}', 'CalculatorKPController@pesticides_f');
    Route::get('/calculator_kp/object/', 'CalculatorKPController@object_v');
    Route::get('/calculator_kp/season/', 'CalculatorKPController@season');
    Route::get('/calculator_kp/filter_prod/{name}/', 'CalculatorKPController@name_c');
    Route::get('/calculator_kp/filter_prod_t/{name_c}', 'CalculatorKPController@name_p');
    Route::get('/calculator_kp/filter_prod_j/', 'CalculatorKPController@joint');

    Route::get('/calculator_kp/{user_id}', 'CalculatorKPController@showID');
    Route::post('/calculator_kp/create', 'CalculatorKPController@create');
    Route::post('/calculator_kp/update/{id}', 'CalculatorKPController@update');
    Route::delete('/calculator_kp/delete/{id}', 'CalculatorKPController@delete');

    //end calculator/
    // client add/
    Route::post('/add/client', 'ClientController@show_all_order');
    //
    //client route//
    Route::get('/client/order_all/{id}', 'ClientController@add');
    //end client route

    //staff route
    Route::get('/staff/dashboard/revenue/{iin}', 'StaffController@staff_sales_order');
    Route::get('/staff/dashboard/count_orders/{iin}', 'StaffController@staff_sales_count_order');
    Route::get('/staff/dashboard/plan_season/{iin}', 'StaffController@staff_plan_season');
    Route::get('/staff/dashboard/mini_profile/{iin}', 'StaffController@staff_mini_p');
    Route::get('/staff/contract/{iin}', 'StaffController@staff_contracts');
    Route::get('/staff/contract/dop/count/{guid}', 'StaffController@contracts_count');
    //

    //parser contactlist
    Route::get('/semena/all', 'MapsDataController@semena');
    Route::post('/semena/changedlist', 'MapsDataController@semena_wr');
    Route::get('/user/photo/{iin}', 'ContactListController@user_photo');
    Route::get('/parser/', 'ParserController@pars');
    Route::post('/parser_list', 'ParserController@eldala');
    Route::get('/parser_list2', 'ParserController@eldaladetail');
    Route::get('/parser_list/margin', 'ParserController@margin');
    //end contactlist
    //DoGovor

    Route::post('/dg','UtilXMLController@dg');
    Route::post('/dg_p','UtilXMLController@dg_guid');

    //end dg

    //PRODUCTS SEARCH ROW NAME LIKE
    Route::get('/product/{name}', 'ProductController@product_search');
    //END PRODUCY SEARCH 
    ///adress search row name like
    Route::get('/adress/{name}', 'ProductController@adress_search');
    ///END 


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
    
    //Api for semena table select all 
    Route::get('/semena/all', 'StaffController@SemenaSelect');
    Route::post('/semena/changedlist', 'StaffController@SemenaUpdate');
    //end
    
    
    ///test route for maps
    Route::get('/country','StaffController@Region');
    Route::get('/district/{cato}','StaffController@PolygonCLient');
    Route::get('/region/{kat_f}','StaffController@District');
    Route::get('/district_new/{cato}', 'StaffController@ClientFields');
    Route::get('/elevatorMarker/', 'StaffController@ElevatorMarker');
    Route::post('/clientDistrictFields/','StaffController@clientDistrictFields'); /// ----
    Route::get('/getCultureSpr/{region}', 'StaffController@GetCultureSpr');
    Route::post('/filterFields','StaffController@FilterClientFields');
    Route::get('/clientPolygons/{guid}', 'StaffController@ClientGuid');
    Route::get('/clientFieldsCult/{id}', 'StaffController@ClientGroupCulture');//---
    Route::post('/getClientFieldsCult', 'StaffController@ClientFieldGuid'); ///----
    Route::get('/clientPolygon/{id}', 'StaffController@PolygonDetail');
    Route::get('/clientField/{id}', 'StaffController@FieldsDetail'); /// ----
    Route::get('/usersmigration', 'StaffController@Migrationalluser');
    Route::get('/historyBrowsing/List/{user_id}', 'StaffController@HistoryBrowsingList');
    Route::get('/historyBrowsing/Detail/{id}', 'StaffController@HistoryBrowsingDetail');
    Route::post('/historyBrowsing/Create', 'StaffController@HistoryBrowsingCreate');
    Route::get('/testdo/{id}', 'StaffController@todol');
    Route::post('/testdo/v2/', 'StaffController@TestClientFields');
    //end route for maps 

    //check statgov 
    Route::get('/statgor', 'StaffController@CheckClient');
    //end 

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
    //end api for web 
    //maps 
    
    Route::get('/maps/coordinate_marker/{cato}', 'GeoController@show_district'); 

    Route::get('/aisgzk/{id}', 'GeoController@aisgzk');

    Route::get('/maps/coordinate_marker_longi/', 'UtilXMLController@coordinate_f_ele'); 

    Route::get('/maps/coordinate_marker_route','GeoController@router_order');

    Route::get('/maps/today_order/{name}','GeoController@todayorder');
    
    Route::get('/maps/coordinate_marker_route/guid/{guid}','GeoController@router_client');

    Route::get('/maps/list/warehouse','GeoController@warehouse_aa');
    
    Route::get('/maps/list/warehouse/{guid}','GeoController@warehouse_to');

    Route::get('/maps/coordinate_marker_other', 'GeoController@show_all_other'); 

    Route::get('/maps/store_coord/', 'CoordinatesController@store_coord'); // store_coord

    Route::get('/maps/detail_client/{iin}/{date}', 'GeoController@detail_client');    

    Route::get('/maps/manager_region/{cato}', 'GeoController@manager_region');

    Route::post('/maps/manager_region/', 'GeoController@manager_filter');

    Route::get('/maps/manager_region/', 'GeoController@list_data');

    Route::get('/maps/cato_active/{kato_id}', 'GeoController@active_region');

    Route::get('/maps_data/{id}', 'MapsDataController@showID');

    Route::get('/coordinates', 'CoordinatesController@all');

    Route::get('/coordinates_warehouse', 'CoordinatesController@coordinate_warehouse');

    Route::get('/coord', 'CoordinatesController@coord');

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

//task manager
    Route::get('tasks', 'TasksController@all');
    Route::get('/tasks/{id}', 'TasksController@showID');
    Route::post('/tasks/create/', 'TasksController@create');
    Route::post('/tasks/update/{id}', 'TasksController@update');
    Route::delete('/tasks/delete/{id}', 'TasksController@delete');
    //выводить список заявок по компании на расходники, доделать с кодировкой проблемы
    Route::get('/list_order', 'TasksController@task_status');
    

//опросник
    Route::get('survey', 'SurveyController@all');
    Route::get('/survey/{id}', 'SurveyController@showID');
    Route::post('/survey/create/', 'SurveyController@create');
    Route::post('/survey/update/{id}', 'SurveyController@update');
    Route::delete('/survey/delete/{id}', 'SurveyController@delete');

//зп
    Route::post('/wage/seep_d', 'MapsDataController@wage_d_s');
    Route::post('/wage/seep_s', 'MapsDataController@wage_s');

});
