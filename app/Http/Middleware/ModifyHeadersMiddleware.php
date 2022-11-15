<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ModifyHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next( $request );
        //Сначала разрешим принимать и отправлять запросы на сервер А
        $response->header( 'Access-Control-Allow-Origin', '*' );
        //Установим типы запросов, которые следует разрешить (все неуказанные будут отклоняться)
        $response->header( 'Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, UPDATE, OPTIONS' );
        //Разрешим передавать Cookie и Authorization заголовки для указанновго в Origin домена
        $response->header( 'Access-Control-Credentials', 'true' );
        //Установим заголовки, которые можно будет обрабатывать
        $response->header( 'Access-Control-Allow-Headers', 'Authorization, Origin, X-Requested-With, Accept, X-PINGOTHER, Content-Type, X-Auth-Token' );

        return $response;

        //return $next($request);
    }
}
