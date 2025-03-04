<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateReferer
{
    public function handle(Request $request, Closure $next)
    {
        $allowedReferrers = [ parse_url(config('app.url'), PHP_URL_HOST)];
        if ($request->headers->has('referer')) {
            $referer = parse_url($request->headers->get('referer'), PHP_URL_HOST);

            if (!in_array($referer, $allowedReferrers)) {
                abort(403, 'Invalid referer.');
            }
        }

        return $next($request);
    }
}
