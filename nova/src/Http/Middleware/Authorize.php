<?php

namespace Laravel\Nova\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Nova;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        if (Auth::user()->password_block)
        {
            Auth::guard(config('nova.guard'))->logout();

            $request->session()->invalidate();
            return redirect('/nova/password/change_password');
        }
        return Nova::check($request) ? $next($request) : abort(403);
    }
}
