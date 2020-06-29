<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Multicaret\Unifonic\UnifonicFacade;
 
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'mobile_sent_at', 'mobile_active_code', 'gender',

        //business info
        'business_name', 
        'sector',
        'website',
        'twitter',
        'facebook',
        'instagram',
        'logo',
        'description',
        'business_address',
        'business_mobile',
        'vat_registration_number',
        
        //bank info
        'license_type',
        'bank',
        'iban_number',
        'organization_name',
        'beneficiary_name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'mobile_active_code'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_sent_at' => 'datetime',
    ];


    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getMobileVerifiedAttribute()
    {
        return (bool) !isset($this->mobile_sent_at);
    }   

    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function sendMobileCode()
    {
        $mobile_active_code = str_pad(rand(0, pow(10, 4)-1), 4, '0', STR_PAD_LEFT);
        $this->mobile_sent_at = Carbon::now();
        $this->mobile_active_code = !app()->environment('production') ? '0000' : $mobile_active_code;
        $this->save();
        if(app()->environment('production')){
            $message = __('verification code : ',[],'en') . $mobile_active_code;
            $message .= PHP_EOL;
            $mobile = (int) $this->mobile;
            $mobile = (int) '966'.$mobile;
            UnifonicFacade::send($mobile, $message);
        }
    }

    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getGravatarAttribute()
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash";
    }
}
