<?php

namespace App\Console\Commands;

use App\Events\TransferCreated;
use App\Exports\BillsExport;
use App\Http\Resources\BillResource;
use App\Mail\AutoTransferMail;
use App\Models\Bank;
use App\Models\Bill;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
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
            $users = User::all();
            foreach ($users as $user) {

                if($user->balance >= $transfer_minimum){
                    $from = $this->getToDate($user);
                    $bills = $this->getbillsBetweenDate($from, $to, $user);
                    $this->sendMails($transfer_emails, $to);
                    $amount = $this->getAmount($bills, $user);

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

                        foreach ($bills as $bill) {
                            if($bill->user_id == $user->id){
                                $bill->settled = true;
                            }

                            if($bill->isHaveChannelOwenByUser($user->id)){
                               $bill->channel_settled = true; 
                            }
                            $bill->save();
                        }
                        $transfer->bills()->attach($bills->pluck('id')->toArray());

                        return $transfer;
                    });
                    event(new TransferCreated($transfer));
                }
            }
        }
    }

    /**
     * get To Date foryouser.
     *
     * @return Carbon\Carbon
     */
    public function getToDate($user)
    {
        $last_transfer = $user->transfers()->latest()->first();
        
        if(isset($last_transfer) && isset($last_transfer->filters['date']['to'])){
            return Carbon::parse($last_transfer->filters['date']['to']);
        }else{
            return $user->created_at;
        }
    }

    /**
     * get bills Between Date.
     *
     * @return \Illuminate\Http\Response
     */
    protected function getbillsBetweenDate($from_s, $to_s, $user)
    {
        $to = $to_s->copy()->endOfDay()->toDateTimeString();
        $from = $from_s->copy()->startOfDay()->toDateTimeString();

        $bills =  Bill::
            //get user bills
            where(function ($query) use($user, $from, $to){
                // dd($from);
                $query->where('user_id', $user->id)
                    ->paid()
                    ->whereBetween('paid_at', [$from, $to])
                    ->where('settled', false);
            })
            //get user "channels" bills
            ->orWhere(function ($query) use($user, $from, $to){
                $query->whereIn('application_id', $user->channelsApplications->pluck('id')->toArray())
                    ->paid()
                    ->whereBetween('paid_at', [$from, $to])
                    ->where('channel_settled', false);
            })
            
            ->orderBy('paid_at', 'asc')
            ->get();

            $this->createExcel($bills, $user, $from, $to, $to_s->timestamp);
        return $bills;

    }


    /**
     * get Amount.
     *
     * @param  App\Bill  $bills
     * @param  App\User  $user
     * @return double
     */
    protected function getAmount($bills, $user)
    {
        $billsids = $bills->pluck('id')->toArray();
        $transactions = Transaction::whereIn('bill_id', $billsids)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'ASC')
            ->orderBy('receipt', 'ASC')
            ->get();
        return round($transactions->where('type', 'credit')->sum('amount')-$transactions->where('type', 'debit')->sum('amount'), 2);
    }    

    /**
     * create Excel.
     *
     * @param  App\Bill  $bills
     * @param  App\User  $user
     * @param  Carbon\Carbon  $from
     * @param  Carbon\Carbon  $to
     * @param  integer  $timestamp
     * @return boolean
     */
    protected function createExcel($bills, $user, $from, $to, $timestamp)
    {
        $title = "bills/$timestamp/Bills-{$user->business_name_en}-FROM-{$from}-TO-{$to}.xlsx";
        $data = json_decode((BillResource::collection($bills))->toJson(), true);
        return Excel::store(new BillsExport($data), $title);
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
