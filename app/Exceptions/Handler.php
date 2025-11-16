<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect('/login');
        }

        // Handle throttle exceptions for OTP routes
        if ($exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
            // Check if it's an OTP-related route
            if ($request->is('verify-otp') || $request->is('resend-otp')) {
                $retryAfter = $exception->getHeaders()['Retry-After'] ?? config('merchant_otp.throttle_time', 5);
                
                return back()->withErrors([
                    'message' => __('Too many attempts. Please try again in :minutes minutes.', [
                        'minutes' => ceil($retryAfter / 60)
                    ])
                ]);
            }
        }

        return parent::render($request, $exception);
    }
}
