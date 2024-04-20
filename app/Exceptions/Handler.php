<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];
 /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Проверяем, ожидает ли запрос JSON ответ и является ли исключение связанным с аутентификацией
        if ($request->expectsJson()) {
            if ($exception instanceof AuthenticationException) {
                // Возвращаем JSON ответ с кодом 401 Unauthorized
                return response()->json(['message' => 'Неавторизован.'], 401);
            }
        }

        // Вызываем базовый метод для остальных случаев
        return parent::render($request, $exception);
    }
    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // Проверяем, ожидает ли запрос JSON (такое обычно бывает в API)
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Неавторизован'], 401);
        }
    }
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    
}
