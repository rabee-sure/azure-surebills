<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ZatcaApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $zatca_api_key = config('zatca.zatca_api_key');
        if($request->header('X-ZATCA-API-Key') != $zatca_api_key){
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }
        return $next($request);
    }
}
