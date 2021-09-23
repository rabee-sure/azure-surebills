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

        $cycleDate = Carbon::now()->startOfDay();
        if($transfer_automatic && $cycleDate->dayOfWeek == $transfer_day ){
            $users = User::where('verified', true)->where('auto_trnasfer', true)->get();
            
            $filtered_users = $users->filter(function($user) use($transfer_minimum){
                return $user->actual_balance >= $transfer_minimum;
            });
            
            foreach ($filtered_users as $user) {
                $amount = $user->getBalanceBefore($cycleDate->format('Y-m-d'));
                if($amount  >= $transfer_minimum){
                    $this->info("transfer to user ID $user->id amount: $amount");

                    $bank = $user->bank;
                    $transfer_fees = $bank->fees + ($bank->fees * 0.15);
                    $data = [
                        'cycle_date' => $cycleDate,
                        'transfer_fees' => $transfer_fees,
                        'note' => 'automatic transfer',
                        'created_by_id' => null,
                        'bank_id' => $bank->id,
                        'user_id' => $user->id,
                        'iban_number' => $user->iban_number,
                        'beneficiary_name' => $user->beneficiary_name,
                    ];
                    $transfer = TransferService::makeTransfer('pending', $amount, $data);
                }
            }

            // if($filtered_users->count())
                // $this->sendMails($transfer_emails, $cycleDate);
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
