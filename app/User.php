<?php

namespace App;

use App\Notifications\ResetPassword;
use App\Transaction;
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
        'license_type',
        'organization_name',
        
        //bank_id info
        'bank_id',
        'iban_number',
        'beneficiary_name',

        //bank_id princing
        'price_percentage',
        'price_fixed',
        'pay_fees',
        
        'mobile_verified',
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
        $transactions = $this->transactions;
        $deposits = $transactions->where('type', 'credit')->sum('amount');
        $withdraws = $transactions->where('type', 'debit')->sum('amount');

        return $deposits - $withdraws;
    }


    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getRoundBalanceAttribute()
    {
        return round($this->balance, 2);
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

            isset($this->bank_id)&&
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
     * Get Transfers.
     *
     * @return Collection
     */
    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    /**
     * Get settings.
     *
     * @return Collection
     */
    public function settings()
    {
        return $this->hasOne(Settings::class);
    }

    /**
     * Get bank.
     *
     * @return Collection
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get statement.
     *
     * @return Collection
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
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

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}
