<?php

namespace App\Http\Middleware;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Closure;

class MultiAuth
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
        if(($request->hasHeader('X-application-id') && $request->hasHeader('X-application-secret') || ($request->application_id && $request->application_secret))){
            $application_id = $request->hasHeader('X-application-id') ? $request->header('X-application-id') : $request->application_id;
            $application_secret = $request->hasHeader('X-application-secret') ? $request->header('X-application-secret') : $request->application_secret;
            $application = Application::whereId($application_id)
            ->whereSecret($application_secret)
            ->first();
            
            if (isset($application->user)) {
                $request->setUserResolver(function () use ($application) {
                    return $application->user;
                });
                $request->merge([
                    'user' => $application->user,
                    'application' => $application,
                ]);
                return $next($request);
            }
            return response('Unauthorized.', 401);
        }elseif($request->hasHeader('Authorization')){
            $api = Auth::guard('api')->user();
            if ($api) {
                return $next($request);
            }
            return response('Unauthorized.', 401);
        }
    }
}
