<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RedirectRouteToMainDomainMiddleware
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
        $subdomain = config('payment.invoice_subdomain');
        $mainDomain = parse_url(config('app.url'), PHP_URL_HOST);
        
        if($request->getHost() == config('payment.invoice_subdomain') && in_array(Route::currentRouteName(), ['mastercard.3ds']))
        {
            return redirect()->to(str_replace($subdomain, $mainDomain, $request->fullUrl()));
        }
        return $next($request);
    }
}
