<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, HasApiTokens, Notifiable, InteractsWithMedia, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $userId = null;
    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'mobile_sent_at', 'mobile_active_code', 'gender', 'store_main_user_id',

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
        'business_address_details',
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
        'able_refund_with_fees',
        'auto_trnasfer',
        'from_channel_id',
        'vat_inclusive',
        'bullding_no',
        'street_name',
        'district',
        'city',
        'postal_code',
        'additional_no',
        'other_buyer_id'
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
        'able_refund' => 'boolean',
        'vat_inclusive' => 'boolean',
        'auto_trnasfer' => 'boolean',
        'disable_business_documents' => 'boolean',
        'disable_bank_documents' => 'boolean',
        'verified' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::updated(function(User $user){
            User::where('store_main_user_id', $user->id)->update(array('verified' => $user->verified, 'able_refund' => $user->able_refund,
                                                                        'vat_inclusive' => $user->vat_inclusive,
                                                                        'able_refund_with_fees' => $user->able_refund_with_fees,
                                                                        'disable_business_documents' => $user->disable_business_documents,
                                                                        'disable_bank_documents' => $user->disable_bank_documents,
                                                                        'auto_trnasfer' => $user->auto_trnasfer));
        });
    }

    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getBalanceAttribute()
    {
        if($this->userId)
        {
            $user = Transaction::userId($this->userId);
        }
        else
        {
            $user = $this->transactions();
        }

        $user = $user
            ->select(DB::raw("SUM(CASE WHEN type  = 'credit' THEN amount ELSE 0 END) AS credit_total,SUM(CASE WHEN type  = 'debit' THEN amount ELSE 0 END) AS debit_total"))
            ->first();
        $balance = $user->credit_total - $user->debit_total;
        return floorp($balance, 2);
    }

    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getPendingBalanceAttribute()
    {
        if($this->userId)
        {
            $transfer = Transfer::userId($this->userId);
        }
        else
        {
            $transfer = $this->transfers();
        }

        $transfer = $transfer
            ->select(DB::raw("SUM(CASE WHEN status  = 'pending' THEN amount ELSE 0 END) AS total"))
            ->first();
        return $transfer->total;
    }


    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getPaidCashBalanceAttribute()
    {
        if($this->userId)
        {
            $bills_paid_cash = Bill::userId($this->userId);
        }
        else
        {
            $bills_paid_cash = $this->bills();
        }

        $bills_paid_cash = $bills_paid_cash
            ->select(DB::raw("SUM(CASE WHEN status  = 'paid_cash' THEN total ELSE 0 END) AS totals"))
            ->first();
        return $bills_paid_cash->totals;
    }


    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getPaidBankTransferBalanceAttribute()
    {
        if($this->userId)
        {
            $bills_paid_cash = Bill::userId($this->userId);
        }
        else
        {
            $bills_paid_cash = $this->bills();
        }

        $bills_paid_cash = $bills_paid_cash
            ->select(DB::raw("SUM(CASE WHEN status  = 'paid_bank_transfer' THEN total ELSE 0 END) AS totals"))
            ->first();
        return $bills_paid_cash->totals;
    }

    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getActualBalanceAttribute()
    {
        $balance = $this->balance - $this->pending_balance;
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
        return ' ' . $this->balance;
    }

    public function getVerifyStatusAttribute()
    {
        if ($this->verified == 1) {
            return __('yes');
        } else {
            return __('no');
        }
    }

    public function getLogoUrlAttribute()
    {
        if (Storage::disk('public')->exists($this->logo))
            return url('storage/' . $this->logo);
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
        return ((isset($this->business_name_en) && !empty($this->business_name_en) &&
            isset($this->business_address) && !empty($this->business_address) &&
            isset($this->business_address) && !empty($this->business_address) &&

            isset($this->bank_id) && !empty($this->bank_id) &&
            isset($this->iban_number) && !empty($this->iban_number) &&
            isset($this->beneficiary_name) && !empty($this->beneficiary_name)) ||
            $this->mainStoreUser);
    }

    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function sendMobileCode()
    {
        $mobile_active_code = str_pad(rand(0, pow(10, 4) - 1), 4, '0', STR_PAD_LEFT);
        $this->mobile_sent_at = Carbon::now();
        $this->mobile_active_code = !app()->environment('production') ? '0000' : $mobile_active_code;
        $this->save();
        if (app()->environment('production')) {
            $message = __('verification code : ', [], 'en') . $mobile_active_code;
            $message .= PHP_EOL;

            $mobile = (int) $this->mobile;
            $data = ["Tagname" => "SURE-Pay", "RecepientNumber" => "0" . $mobile, "Message" => $message, "Username" => env('YAMAMAH_USERNAME'), "Password" => env('YAMAMAH_PASSWORD')];
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
        return (isset($this->business_name_ar) && app()->getLocale() == 'ar') ? $this->business_name_ar : $this->business_name_en;
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

    public function getApplication($name)
    {
        return $this->applications->where('name', $name)->first();
    }

    /**
     * Get last transfer.
     *
     * @return Collection
     */
    public function lastTransferTransaction()
    {
        if($this->userId)
        {
            $user = Transaction::userId($this->userId);
        }
        else
        {
            $user = $this->transactions();
        }

        return $user->where('transaction_source', 'transfer')->latest()->first();
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
        return $this->hasMany(Customer::class, 'user_id', 'id');
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

    public function billsCreatedByMe(){
        return $this->hasMany(Bill::class, 'created_by', 'id');
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
     * Get settings.
     *
     * @return Collection
     */
    public function mainStoreUser()
    {
        return $this->belongsTo(User::class, 'store_main_user_id', 'id');
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
        return Transaction::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)
            ->orderBy('created_at', 'ASC')
            ->orderBy('order', 'ASC')
            ->orderBy('receipt', 'ASC');
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


        $channel = (request()->has('channel_id') && !in_array(request()->channel_id, ['all', 'undefined'])) ? Channel::find(request()->channel_id) : null;
        $application = (request()->has('application_id') && !in_array(request()->application_id, ['all', 'undefined'])) ? Application::find(request()->application_id) : null;

        return $this->statement()
            ->when($date_start, function ($q) use ($date_start, $date_to) {
                $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                    ->whereDate('created_at', '<=', Carbon::parse($date_to));
            })
            ->when(request()->transaction_type == 'debit' || request()->transaction_type == 'credit', function ($q) {
                $q->whereType(request()->transaction_type);
            })
            ->when(isset(request()->transaction_source) && request()->transaction_source != 'all' && request()->transaction_source != 'undefined', function ($q) {
                $q->whereTransactionSource(request()->transaction_source);
            })
            ->when($channel, function ($q) use ($channel) {
                $q->whereHas('bill.application', function ($query) use ($channel) {
                    $query->where('channel_id', $channel->id);
                });
            })
            ->when($application, function ($q) use ($application) {
                $q->whereHas('bill', function ($query) use ($application) {
                    $query->where('application_id', $application->id);
                });
            })->with('bill')
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

    /**
     * Vrification Request
     *
     * @param  string  $token
     * @return void
     */
    public function getBalanceBefore($date)
    {
        if($this->userId)
        {
            $user = Transaction::userId($this->userId);
        }
        else
        {
            $user = $this->transactions();
        }

        $balance_total = $user
            ->amountByCycleDate($date)
            ->select(DB::raw("SUM(CASE WHEN type  = 'credit' THEN amount ELSE 0 END) AS credit_total,SUM(CASE WHEN type  = 'debit' THEN amount ELSE 0 END) AS debit_total"))
            ->first();
        $balance =  $balance_total->credit_total - $balance_total->debit_total;
        return floorp($balance, 2);
    }

    public function getAuthUser($token = null)
    {
        if($token){
            return auth('api')->user();
        }else{
            return Auth::user();
        }
    }
}
