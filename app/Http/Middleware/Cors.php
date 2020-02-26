<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Log::INFO('CORS対策Middleware実行');
        $response = $next($request);
        $httpOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";

        // Log::DEBUG("httpOrigin : " . $httpOrigin);

        $response
            ->header('Access-Control-Allow-Origin', 'http://localhost:3000')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');

        return $response;
    }
}