<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use Multicaret\Unifonic\UnifonicFacade;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, HasApiTokens, Notifiable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'mobile_sent_at', 'mobile_active_code', 'gender',

        //business info
        'business_name_en',
        'business_name_ar',
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
        'commercial_registry_expiry_date',

        //bank_id info
        'bank_id',
        'iban_number',
        'beneficiary_name',

        //bank_id princing
        'price_percentage',
        'price_fixed',
        'pay_fees',

        'mobile_verified',
        'disable_business_documents',
        'disable_bank_documents',

        'able_refund',
        'auto_trnasfer',
        'from_channel_id',
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
        'commercial_registry_expiry_date' => 'datetime',
        'verified' => 'boolean',
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
        $balance = $deposits - $withdraws;
        return $balance;
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

    public function getBalanceStringAttribute()
    {
        return ' '.$this->balance;
    }

    public function getVerifyStatusAttribute()
    {
        if($this->verified == 1){
            return __('yes');
        }
        else{
            return __('no');
        }
    }

    public function getLogoUrlAttribute()
    {
        if(Storage::disk('public')->exists($this->logo))
            return url('storage/'.$this->logo);
        else
            return url($this->logo);
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
            isset($this->business_name_en)&& !empty($this->business_name_en)&&
            isset($this->business_address)&& !empty($this->business_address)&&
            isset($this->business_address)&& !empty($this->business_address)&&

            isset($this->bank_id) && !empty($this->bank_id) &&
            isset($this->iban_number) && !empty($this->iban_number) &&
            isset($this->beneficiary_name) && !empty($this->beneficiary_name)
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
        if(app()->environment('production'))
        {
            $message = __('verification code : ',[],'en') . $mobile_active_code;
            $message .= PHP_EOL;

            $mobile = (int) $this->mobile;
            $data = ["Tagname" => "SURE-Pay", "RecepientNumber" => "0".$mobile, "Message" => $message, "Username" => env('YAMAMAH_USERNAME'), "Password" => env('YAMAMAH_PASSWORD')];
            $payload = json_encode($data);
            $ch = curl_init('http://api.yamamah.com/SendSMS');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($payload)));
            $result = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($result, true);

            // $mobile = (int) $this->mobile;
            // $mobile = (int) '966'.$mobile;
            // UnifonicFacade::send($mobile, $message);
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
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getBusinessNameAttribute()
    {
        return (isset($this->business_name_ar) && app()->getLocale() == 'ar')?$this->business_name_ar : $this->business_name_en;
    }

    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getBusinessNameSlugAttribute()
    {
        return str_slug($this->business_name_en, '_');
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
     * Get last transfer.
     *
     * @return Collection
     */
    public function lastTransferTransaction()
    {
        return $this->transactions()->where('transaction_source', 'transfer')->latest()->first();
    }

    /**
     * Get applications.
     *
     * @return Collection
     */
    public function channelsApplications()
    {
        return $this->belongsToMany(Application::class, 'channels', 'user_id', 'id', 'id', 'channel_id');
    }

    /**
     * Get customers.
     *
     * @return Collection
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
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
     * Get channels.
     *
     * @return Collection
     */
    public function channels()
    {
        return $this->hasMany(Channel::class)->activate();
    }    

    /**
     * Get channels.
     *
     * @return Collection
     */
    public function fromChannel()
    {
        return $this->belongsTo(Channel::class, 'from_channel_id');
    }

    /**
     * Get statement.
     *
     * @return Collection
     */
    public function statement()
    {
        return $this->hasMany(Transaction::class)
            ->orderBy('created_at', 'ASC')
            ->orderBy('order', 'ASC')
            ->orderBy('receipt', 'ASC')
            ;
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

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function getIsUploadedDocumentsAttribute()
    {
        return $this->getMedia('business_documents')->count() && $this->getMedia('bank_documents')->count();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function getIsUploadedBusinessDocumentsAttribute()
    {
        return $this->getMedia('business_documents')->count();
    }    

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function getIsUploadedBankDocumentsAttribute()
    {
        return $this->getMedia('bank_documents')->count();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function getTwoBusinessDaysAttribute()
    {
        $last_business_documents = $this->getMedia('business_documents')->last()->created_at;
        $last_bank_documents = $this->getMedia('bank_documents')->last()->created_at;
        // dd($last_bank_documents);
        $result = $last_business_documents->gt($last_bank_documents);
        $date = ($result) ? $last_business_documents : $last_bank_documents;
        return $date->addDays(2)->format('d/m/Y');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function getBusinessDocumentsAttribute($token)
    {
        return $this->getMedia('business_documents');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function getStatement()
    {
        $date_start = request()->date_start ?? Carbon::today()->firstOfMonth()->format('m/d/Y');
        $date_to = request()->date_to ?? Carbon::today()->format('m/d/Y');


        $channel = (request()->has('channel_id') && !in_array(request()->channel_id, ['all','undefined']))? Channel::find(request()->channel_id) : null;
        $application = (request()->has('application_id') && !in_array(request()->application_id, ['all','undefined']))? Application::find(request()->application_id) : null;

        return $this->statement()
            ->when($date_start, function($q) use($date_start, $date_to){
                $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                    ->whereDate('created_at', '<=', Carbon::parse($date_to));
            })
            ->when(request()->transaction_type == 'debit' || request()->transaction_type == 'credit', function($q) {
                $q->whereType(request()->transaction_type);
            })
            ->when(isset(request()->transaction_source) && request()->transaction_source != 'all' && request()->transaction_source != 'undefined', function($q){
                $q->whereTransactionSource(request()->transaction_source);
            })
            ->when($channel, function($q) use($channel){
                $q->whereHas('bill.application', function ( $query) use($channel){
                    $query->where('channel_id', $channel->id);
                });
            })
            ->when($application, function($q) use($application){
                $q->whereHas('bill', function ( $query) use($application){
                    $query->where('application_id', $application->id);
                });
            })
            ->get();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function getBankDocumentsAttribute($token)
    {
        return $this->getMedia('bank_documents');
    }

    /**
     * Vrification Request
     *
     * @param  string  $token
     * @return void
     */
    public function scopeVrificationRequest($query)
    {
        return $query->where('verified', false)
                ->whereNotNull([
                    'business_name_en',
                    'business_address',
                    'business_mobile',
                ]);
    }

}
