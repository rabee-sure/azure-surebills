<?php

namespace App\Notifications;

use App\Channels\SmsChannel;
use App\Services\SMSService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The OTP code.
     *
     * @var string
     */
    protected $otp;

    /**
     * The notification channels.
     *
     * @var array
     */
    protected $channels;

    /**
     * Create a new notification instance.
     *
     * @param  string  $otp
     * @param  array  $channels
     * @return void
     */
    public function __construct($otp, $channels = ['mail'])
    {
        $this->otp = $otp;
        $this->channels = $channels;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Map 'sms' to custom SMS channel
        return array_map(function($channel) {
            return $channel === 'sms' ? SmsChannel::class : $channel;
        }, $this->channels);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Your Login OTP Code'))
            ->greeting(__('Hello!'))
            ->line(__('Your OTP code for login verification is:'))
            ->line('**' . $this->otp . '**')
            ->line(__('This code will expire in :minutes minutes.', ['minutes' => config('merchant_otp.expiration_minutes')]))
            ->line(__('If you did not attempt to log in, please ignore this email.'))
            ->salutation(__('Thank you for using our application!'));
    }

    /**
     * Get the SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toSms($notifiable)
    {
        return __('Your login OTP code is: :otp. This code will expire in :minutes minutes.', ['otp' => $this->otp, 'minutes' => config('merchant_otp.expiration_minutes')]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'otp' => $this->otp,
            'expires_in' => config('merchant_otp.expiration_minutes') . ' minutes',
        ];
    }
}
