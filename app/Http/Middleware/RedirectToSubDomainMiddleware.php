<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RedirectToSubDomainMiddleware
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
        if(config('app.env') != 'local')
        {
            if(Route::currentRouteName() == 'paybillpage')
            {
                return redirect(route('bill.invoice.subdomain', ['id' => $request->id]));
            }
            else if(Route::currentRouteName() == 'paybillpagelang')
            {
                return redirect(route('bill.invoice.lang.subdomain', ['id' => $request->id, 'lang' => $request->lang]));
            }    
        }

        return $next($request);
    }
}
