<?php

namespace App\Http\Controllers;

use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MobileVerifyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->mobile_verified || !isset($user->mobile_sent_at)) {
            return redirect('home');
        }

        $resendRemainingSeconds = OtpService::secondsUntilResend($user->mobile_sent_at);

        return view('mobile_verify', [
            'user' => $user,
            'resendRemainingSeconds' => $resendRemainingSeconds,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->mobile_verified) {
            return redirect('home');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'otp' => ['required', 'string', 'size:4'],
            ],
            [
                'otp.required' => __('The OTP is required.'),
                'otp.string' => __('The OTP must be a string.'),
                'otp.size' => __('The OTP must be 4 digits.'),
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($user->mobile_active_code == $request->otp) {
            $user->mobile_sent_at = null;
            $user->mobile_verified = true;
            $user->save();

            return redirect('home');
        }

        return back()->withErrors([
            'otp' => __('invalid PIN'),
        ])->withInput();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function resendCode(Request $request)
    {
        $user = auth()->user();
        if ($user->mobile_verified) {
            return redirect('home');
        }

        if (OtpService::secondsUntilResend($user->mobile_sent_at) > 0) {
            return back();
        }

        $user->sendMobileCode();

        return back()->with('status', __('A new OTP has been sent to your mobile number.'));
    }
}
