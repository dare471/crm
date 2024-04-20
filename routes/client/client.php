<?php // routes/client.php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['api', 'auth:api'],
    'namespace' => 'App\Http\Controllers\client',
    'prefix' => 'client',
], function () {
    // Основные операции с клиентами через RESTful Resource Controller
    Route::resource('/', 'ClientController')->only([
        'index', 'show', 'store', 'update', 'destroy'
    ]);

    // Подресурсы клиента, такие как контакты, культуры, комментарии и визиты
    // Использование Route::resource для каждого подресурса
    Route::prefix('/{clientId}')->group(function () {
        // Контакты клиентов
        Route::resource('/contact', 'ClientContactController')->only([
            'index', 'show', 'store', 'update', 'destroy'
        ]);

        // Культуры клиентов
        Route::resource('/crop', 'ClientCropController')->only([
            'index', 'show', 'store', 'update', 'destroy'
        ]);

        // Комментарии к клиенту
        Route::resource('/note', 'ClientNoteController')->only([
            'index', 'show', 'store', 'update', 'destroy'
        ]);

        // Визиты к клиенту
        Route::resource('/visit', 'ClientVisitController')->only([
            'index', 'show', 'store', 'update', 'destroy'
        ]);
    });
});

// Здесь заканчивается определение маршрутов API для клиента
