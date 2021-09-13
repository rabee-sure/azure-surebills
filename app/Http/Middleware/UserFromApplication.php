<?php

namespace App\Http\Middleware;

use App\Models\Application;
use Closure;

class UserFromApplication
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
        $application_id = $request->hasHeader('X-application-id') ? $request->header('X-application-id') : $request->application_id;
        $application_secret = $request->hasHeader('X-application-secret') ? $request->header('X-application-secret') : $request->application_secret;
        // dd([
        //     $application_id,
        //     $application_secret
        // ]);
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
    }
}
