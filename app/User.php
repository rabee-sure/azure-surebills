<?php

namespace App;

use Carbon\Carbon;
use App\Transaction;
use Laravel\Passport\HasApiTokens;
use Multicaret\Unifonic\UnifonicFacade;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
 
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
        'license_type',
        'organization_name',
        
        //bank info
        'bank',
        'iban_number',
        'beneficiary_name',

        //bank princing
        'price_percentage',
        'price_fixed',
        'pay_fees'
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
    public function getBalanceAttribute()
    {
        $deposits = Transaction::where('user_id', $this->id)->where('type', 'credit')->sum('amount');
        $withdraws = Transaction::where('user_id', $this->id)->where('type', 'debit')->sum('amount');

        return $deposits - $withdraws;
    }   


    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getIsCompleteProfileAttribute()
    {
        return (
            isset($this->business_name)&&
            isset($this->logo)&&
            isset($this->business_address)&&
            isset($this->business_mobile)&&

            isset($this->bank)&&
            isset($this->iban_number)&&
            isset($this->beneficiary_name)
        );
    }  

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
        return "https://www.gravatar.com/avatar/$hash";
    }


    /**
     * Get applications.
     *
     * @return Collection
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get bills.
     *
     * @return Collection
     */
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Get settlements.
     *
     * @return Collection
     */
    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }

    /**
     * Get settlements.
     *
     * @return Collection
     */
    public function settings()
    {
        return $this->hasOne(Settings::class);
    }

    /**
     * Get statement.
     *
     * @return Collection
     */
    public function statement()
    {
        return $this->hasMany(Transaction::class)->orderBy('created_at', 'ASC')->orderBy('receipt', 'ASC');
    }
}
