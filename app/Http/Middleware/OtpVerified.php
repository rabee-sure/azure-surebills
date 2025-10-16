<?php

namespace App\Http\Middleware;

use App\Services\OtpService;
use Closure;
use Illuminate\Http\Request;

class OtpVerified
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks if the user has a pending OTP verification.
     * If OTP is enabled and there's a pending user ID in session,
     * redirect to OTP verification page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if OTP is enabled and there's a pending user in session
        if (OtpService::isEnabled() && session()->has('pending_user_id')) {
            return redirect()->route('otp.verify.form')
                ->with('status', __('Please complete OTP verification to continue.'));
        }

        return $next($request);
    }
}