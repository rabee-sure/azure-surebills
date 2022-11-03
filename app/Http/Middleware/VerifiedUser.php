<?php

namespace App\Http\Middleware;

use App\Models\Application;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifiedUser
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
            $user = isset($application->user) ? $application->user : null;
        }elseif($request->hasHeader('Authorization')){
            $user = Auth::guard('api')->user();
        }else{
            $user = Auth::user();
        }

        if($user && ($user->verified || $user->can_create_bill)){
            return $next($request);
        }
        if ($request->wantsJson()) {
            // return JSON-formatted response
            return response()->json(['authorization' => 'your account not verified and cannot create bill please contant your administrator.'], 401);
        } else {
            // return HTML response
            return redirect()->back()->withErrors(['Unauthorized!' => __("your account not verified and cannot create bill please contant your administrator.")]);
        }
    }
}
