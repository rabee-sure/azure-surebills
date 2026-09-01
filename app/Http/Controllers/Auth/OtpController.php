<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
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
        $this->middleware('guest');
        $this->otpService = $otpService;
    }

    /**
     * Show the OTP verification form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showVerifyForm()
    {
        // Check if there's a pending user in session
        if (!session()->has('pending_user_id')) {
            return redirect()->route('login')->withErrors([
                'email' => __('Your session has expired. Please log in again.'),
            ]);
        }

        $user = User::find(session('pending_user_id'));
        $resendRemainingSeconds = $user
            ? $this->otpService->resendRemainingSeconds($user)
            : 0;

        return view('auth.verify-otp', compact('resendRemainingSeconds'));
    }

    /**
     * Verify the OTP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        // Validate the OTP input
        $validator = Validator::make($request->all(), 
        [
                'otp' => ['required', 'string', 'size:6'],
            ],
            [
                'otp.required' => __('The OTP is required.'),
                'otp.string' => __('The OTP must be a string.'),
                'otp.size' => __('The OTP must be 6 digits.'),
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if there's a pending user in session
        if (!session()->has('pending_user_id')) {
            return redirect()->route('login')->withErrors([
                'email' => __('Your session has expired. Please log in again.'),
            ]);
        }

        // Get the user from session
        $userId = session('pending_user_id');
        $user = User::find($userId);

        if (!$user) {
            session()->forget('pending_user_id');
            return redirect()->route('login')->withErrors([
                'email' => __('User not found. Please log in again.'),
            ]);
        }

        // Verify the OTP
        $result = $this->otpService->verify($user, $request->otp);

        if (!$result['success']) {
            return back()->withErrors([
                'otp' => $result['message'],
            ])->withInput();
        }

        // OTP verified successfully - complete the login
        session()->forget('pending_user_id');
        
        // Log the user in
        Auth::login($user);
        
        // Regenerate session to prevent session fixation
        $request->session()->regenerate();

        // Redirect to intended location or home
        return redirect()->intended('/');
    }

    /**
     * Resend the OTP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        // Check if there's a pending user in session
        if (!session()->has('pending_user_id')) {
            return redirect()->route('login')->withErrors([
                'email' => __('Your session has expired. Please log in again.'),
            ]);
        }

        // Get the user from session
        $userId = session('pending_user_id');
        $user = User::find($userId);

        if (!$user) {
            session()->forget('pending_user_id');
            return redirect()->route('login')->withErrors([
                'email' => __('User not found. Please log in again.'),
            ]);
        }

        if ($this->otpService->resendRemainingSeconds($user) > 0) {
            return back();
        }

        // Generate and send new OTP
        $result = $this->otpService->generateAndSend($user);

        if (!$result['success']) {
            return back()->withErrors([
                'otp' => __('Failed to resend OTP. Please try again.'),
            ]);
        }

        if(config('merchant_otp.channel', 'email') == 'email') {
            return back()->with('status', __('A new OTP has been sent to your email.'));
        } else if(config('merchant_otp.channel', 'email') == 'sms') {
            return back()->with('status', __('A new OTP has been sent to your mobile number.'));
        } else if(config('merchant_otp.channel', 'email') == 'both') {
            return back()->with('status', __('A new OTP has been sent to your email and mobile number.'));
        }
    }
}
