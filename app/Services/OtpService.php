<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserOtp;
use App\Notifications\OtpNotification;
use App\Services\SMSService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * OTP expiration time in minutes.
     *
     * @var int
     */
    protected $expirationMinutes;

    public function __construct()
    {
        $this->expirationMinutes = config('merchant_otp.expiration_minutes');
    }

    /**
     * Generate and send OTP to user.
     *
     * @param  User  $user
     * @return array
     */
    public function generateAndSend(User $user)
    {
        // Generate a random 6-digit OTP
        $otp = $this->generateOtp();

        // Hash the OTP for secure storage
        $otpHash = Hash::make($otp);

        // Store the OTP in database
        $userOtp = UserOtp::create([
            'user_id' => $user->id,
            'otp_hash' => $otpHash,
            'expires_at' => Carbon::now()->addMinutes($this->expirationMinutes),
        ]);

        // Send OTP based on configuration
        try {
            $this->sendOtp($user, $otp);
            
            Log::channel('single')->info('OTP generated for user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'otp_id' => $userOtp->id,
            ]);

            return [
                'success' => true,
                'message' => __('OTP sent successfully'),
                'otp_id' => $userOtp->id,
            ];
        } catch (\Exception $e) {
            Log::channel('single')->error('Failed to send OTP', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => __('Failed to send OTP'),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify the OTP.
     *
     * @param  User  $user
     * @param  string  $otp
     * @return array
     */
    public function verify(User $user, $otp)
    {
        // Get the latest unverified OTP for this user
        $userOtp = UserOtp::latestForUser($user->id)->first();

        if (!$userOtp) {
            return [
                'success' => false,
                'message' => __('No valid OTP found. Please request a new one.'),
            ];
        }

        // Check if OTP is expired
        if ($userOtp->isExpired()) {
            return [
                'success' => false,
                'message' => __('OTP has expired. Please request a new one.'),
            ];
        }

        // Verify the OTP
        if (!Hash::check($otp, $userOtp->otp_hash)) {
            return [
                'success' => false,
                'message' => __('Invalid OTP. Please try again.'),
            ];
        }

        // Mark OTP as verified
        $userOtp->markAsVerified();

        Log::channel('single')->info('OTP verified successfully', [
            'user_id' => $user->id,
            'otp_id' => $userOtp->id,
        ]);

        return [
            'success' => true,
            'message' => __('OTP verified successfully'),
        ];
    }

    /**
     * Generate a random 6-digit OTP.
     *
     * @return string
     */
    protected function generateOtp()
    {
        // Generate a random 6-digit number
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to user based on configuration.
     *
     * @param  User  $user
     * @param  string  $otp
     * @return void
     */
    protected function sendOtp(User $user, $otp)
    {
        $channel = config('merchant_otp.channel', 'email');

        // Send via notification
        if($channel == 'email' || $channel == 'both') {
            $user->notify(new OtpNotification($otp, ['mail']));
        }
        if($channel == 'sms' || $channel == 'both') {
            if (app()->environment('production')) {
                $message = __('Your login OTP code is: :otp. This code will expire in :minutes minutes.', ['otp' => $this->otp, 'minutes' => config('merchant_otp.expiration_minutes')]);
                $message .= PHP_EOL;
    
                $mobile = (int) $user->mobile;
                
                $smsService = new SMSService();
                $response = $smsService->sendSMS($mobile, $message);
                if (!$response) {
                    throw new \Exception(__('Failed to send OTP. Please try again.'));
                }
                Log::channel('single')->info('OTP sent to mobile', [
                    'user_id' => $user->id,
                    'mobile' => $mobile,
                    'message' => $message,
                    'response' => $response,
                ]);
            }
        }
    }

    /**
     * Get notification channels based on configuration.
     *
     * @param  string  $channel
     * @return array
     */
    protected function getNotificationChannels($channel)
    {
        switch ($channel) {
            case 'email':
                return ['mail'];
            case 'sms':
                return ['sms'];
            case 'both':
                return ['mail', 'sms'];
            default:
                return ['mail'];
        }
    }

    /**
     * Clean up expired OTPs.
     *
     * @return int Number of deleted records
     */
    public function cleanupExpired()
    {
        $deleted = UserOtp::expired()->delete();

        Log::channel('single')->info('Expired OTPs cleaned up', [
            'deleted_count' => $deleted,
        ]);

        return $deleted;
    }

    /**
     * Check if OTP is enabled.
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return config('merchant_otp.enabled', false);
    }
}
