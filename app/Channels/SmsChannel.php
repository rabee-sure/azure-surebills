<?php

namespace App\Channels;

use App\Services\SMSService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Get the SMS message from the notification
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);

        // Only send in production environment
        if (app()->environment('production')) {
            $mobile = (int) $notifiable->mobile;
            
            if ($mobile) {
                try {
                    $smsService = new SMSService();
                    $response = $smsService->sendSMS($mobile, $message);
                    
                    Log::channel('single')->info('OTP SMS sent', [
                        'user_id' => $notifiable->id,
                        'mobile' => $mobile,
                        'response' => $response,
                    ]);
                } catch (\Exception $e) {
                    Log::channel('single')->error('Failed to send OTP SMS', [
                        'user_id' => $notifiable->id,
                        'mobile' => $mobile,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } else {
            // In non-production, log the SMS instead of sending
            Log::channel('single')->info('OTP SMS (dev mode)', [
                'user_id' => $notifiable->id,
                'mobile' => $notifiable->mobile,
                'message' => $message,
            ]);
        }
    }
}
