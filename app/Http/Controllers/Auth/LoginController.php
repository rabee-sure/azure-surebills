<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\OtpService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * The maximum number of attempts to allow.
     *
     * @var int
     */
    protected $maxAttempts = 5;

    /**
     * The number of minutes to throttle for.
     *
     * @var int
     */
    protected $decayMinutes = 60;

    /**
     * OTP Service instance.
     *
     * @var OtpService
     */
    protected $otpService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OtpService $otpService)
    {
        $this->middleware('guest')->except('logout');
        $this->otpService = $otpService;
    }

        /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginRequest $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $user = auth()->user();
            $channel = $user->fromChannel;

            // Check if the user is allowed to login
            if(isset($channel) && $channel->disable_login_sub_accounts){
                auth()->logout();
                return $this->sendFailedLoginResponse($request);
            }

            // Check if OTP is enabled
            if (OtpService::isEnabled()) {
                // Logout the user temporarily
                auth()->logout();

                // Store the user ID in session for OTP verification
                $request->session()->put('pending_user_id', $user->id);

                // Generate and send OTP
                $result = $this->otpService->generateAndSend($user);

                if ($result['success']) {
                    // Clear login attempts since credentials are valid
                    $this->clearLoginAttempts($request);

                    // Redirect to OTP verification page
                    if(config('merchant_otp.channel', 'email') == 'email') {
                        return redirect()->route('otp.verify.form')
                            ->with('status', __('An OTP has been sent to your email. Please enter it to complete login.'));
                    } else if(config('merchant_otp.channel', 'email') == 'sms') {
                        return redirect()->route('otp.verify.form')
                            ->with('status', __('An OTP has been sent to your mobile number. Please enter it to complete login.'));
                    } else if(config('merchant_otp.channel', 'email') == 'both') {
                        return redirect()->route('otp.verify.form')
                            ->with('status', __('An OTP has been sent to your email and mobile number. Please enter it to complete login.'));
                    }
                } else {
                    // Failed to send OTP
                    return back()->withErrors([
                        'email' => __('Failed to send OTP. Please try again.'),
                    ]);
                }
            } else {
                // OTP is disabled, proceed with normal login
                return $this->sendLoginResponse($request);
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        return url('/');
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect($this->redirectPath());
    }
}
