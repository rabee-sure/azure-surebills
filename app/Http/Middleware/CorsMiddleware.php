<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
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
        // Check if the request is for a font file
        if (str_contains($request->getRequestUri(), '/fonts/')) {
            $response = $next($request);
            // Add CORS headers
            $response->headers->set('Access-Control-Allow-Origin', 'https://invoice-bills.surepay.sa');
            $response->headers->set('Access-Control-Allow-Methods', 'GET');
            $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
            return $response;
        }

        return $next($request);
    }
}
