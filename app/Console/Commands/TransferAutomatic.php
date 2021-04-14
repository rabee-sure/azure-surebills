<?php

namespace App\Console\Commands;

use App\Events\TransferCreated;
use App\Exports\BillsExport;
use App\Http\Resources\BillResource;
use App\Mail\AutoTransferMail;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Services\TransferService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Valuestore\Valuestore;

class TransferAutomatic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:automatic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'transfer automatic';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $settings =  Valuestore::make(storage_path('app/settings.json'));
        $transfer_automatic = $settings->get('transfer_automatic');
        $transfer_day = $settings->get('transfer_day');
        $transfer_minimum = $settings->get('transfer_minimum');
        $transfer_emails = $settings->get('transfer_emails');

        $to = Carbon::now()->startOfDay();
        if($transfer_automatic && $to->dayOfWeek == $transfer_day ){
            $users = User::where('verified', true)->where('auto_trnasfer', true)->get();
            
            $filtered_users = $users->filter(function($user) use($transfer_minimum){
                return $user->balance >= $transfer_minimum;
            });

            foreach ($filtered_users as $user) {
                $from = TransferService::getFromDate($user);
                $bills = TransferService::getbillsBetweenDate($from, $to, $user);
                $amount = TransferService::getAmount($bills, $user);

                $this->info("transfer to $user->name amount: $amount");

                $transfer = DB::transaction(function () use($user, $bills, $amount){
                    $bank = $user->bank;
                    $transfer = Transfer::create([
                        'status' => 'pending',
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'transfer_fees' => $bank->fees+ ($bank->fees * 0.15),
                        'net_amount' => $amount - $bank->fees+ ($bank->fees * 0.15),
                        'note' => 'automatic transfer',
                        'created_by_id' => null,
                        'bank_id' => $bank->id,
                        'iban_number' => $user->iban_number,
                        'beneficiary_name' => $user->beneficiary_name,
                        'filters' => [
                            'date' => [
                                "from" => '',
                                "to" => '',
                            ]
                        ],
                    ]);

                    $transfer->bills()->attach($bills->pluck('id')->toArray());

                    return $transfer;
                });
            }

            if($filtered_users->count())
                $this->sendMails($transfer_emails, $to);
        }
    }
 

    /**
     * send Mails.
     *
     * @return void
     */
    protected function sendMails($emails_string, $date)
    {
        $emails = explode(",", $emails_string);
        if(count($emails)){
            foreach ($emails as $email) {
                Mail::to($email)->send(new AutoTransferMail($date));
            }
        }
    }
}
