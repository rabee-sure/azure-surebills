<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
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
      if(config('app.env') != 'local') {
 
        $host = $request->getHost();

        $excludeRoutesNamesFromRedirect = ['payment.otp.form', 'verify.applepay.ownership', 'paybillpage', 'paybillpagelang', 'applepay.validate', 'applepay.check-payment', 'bill.invoice.subdomain', 'bill.invoice.lang.subdomain'];
        if($host == config('payment.invoice_subdomain') && !in_array(Route::currentRouteName(), $excludeRoutesNamesFromRedirect))
        {
            return redirect()->to(config('app.url'));
        }
        
      }
        return $next($request);
    }
}
