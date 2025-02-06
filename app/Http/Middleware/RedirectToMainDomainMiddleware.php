<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RedirectToMainDomainMiddleware
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
        $excludeRouteNames = [
            'mastercard.applepay.validate', 
            'mastercard.3ds', 
            'mastercard.applepay.check.payment', 
            'mastercard.handle.payment', 
            'verify.applepay.ownership', 
            'bill.invoice.subdomain', 
            'bill.invoice.lang.subdomain', 
            'applepay.validate', 
        ];
        if($request->getHost() == config('payment.invoice_subdomain') && !in_array(Route::currentRouteName(), $excludeRouteNames))
        {
            return redirect()->to(config('app.url'));
        }

        return $next($request);
    }
}
