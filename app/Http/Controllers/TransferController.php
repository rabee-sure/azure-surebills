<?php

namespace App\Http\Controllers;

use App\Events\TransferCreated;
use App\Http\Resources\TransferResource;
use App\Mail\RequestTransferMail;
use App\Models\Bank;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Services\TransferService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Valuestore\Valuestore;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('transfers.index', ['transfers' => auth()->user()->transfers]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bills(Transfer $transfer, Request $request)
    {
        return view('transfers.bills', [
            'transfer' => $transfer,
            'bills' => $transfer->bills,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function request(Request $request)
    {
        $user = auth()->user();
        $to = Carbon::now()->startOfDay();
        $from = TransferService::getFromDate($user);
        $bills = TransferService::getbillsBetweenDate($from, $to, $user);
        $amount = TransferService::getAmount($bills, $user);
        $settings =  Valuestore::make(storage_path('app/settings.json'));

        $transfer_minimum = $settings->get('transfer_minimum');
        $transfer_emails = $settings->get('transfer_emails');


        if ($transfer_minimum > $amount) {
            return redirect()->back()->withErrors([__('Your balance is not allowed to transfer. The minimum transfer balance is :minimum', ['minimum'=>$transfer_minimum])]);
        }

        if ($user->transfers->where('status', 'pending')->count()) {
            return redirect()->back()->withErrors([__('Sorry, you cannot request a transfer now. Please wait for the Transfer of the previous transfer')]);
        }

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

        if($transfer)
            $this->sendMails($transfer_emails, $to, $transfer);
        
        return redirect()->back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $transfers = Transfer::orderBy('id', 'desc')
            ->where('status', 'pending')
            ->orWhereNull('attachment')
            ->paginate($request->per_page);

        return TransferResource::collection($transfers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fromDate = new Carbon($request->from);
        $fromDate = $fromDate->addDays(1);
        $toDate = new Carbon($request->to);
        $toDate = $toDate->addDays(1);

        $transfer = DB::transaction(function () use($request, $fromDate, $toDate){
            $bank = Bank::find($request->bank_id);
            $transfer = Transfer::create([
                'user_id' => $request->user_id,
                'amount' => $request->amount,
                'transfer_fees' => $bank->fees+ ($bank->fees * 0.15),
                'net_amount' => $request->amount - $bank->fees+ ($bank->fees * 0.15),
                'note' => $request->note,
                'attachment' => $request->attachment,
                'created_by_id' => auth()->user()->id,
                'bank_id' => $request->bank_id,
                'iban_number' => $request->iban_number,
                'beneficiary_name' => $request->beneficiary_name,
                'filters' => [
                    'date' => [
                        "from" => $fromDate,
                        "to" => $toDate,
                    ]
                ],
            ]);

            foreach ($request->bills_ids as $bill_id) {
                $bill = Bill::find($bill_id);
                if($bill->user_id == $request->user_id){
                    $bill->settled = true;
                }

                if($bill->isHaveChannelOwenByUser($request->user_id)){
                   $bill->channel_settled = true; 
                }
                $bill->save();
            }
            $transfer->bills()->attach($request->bills_ids);

            return $transfer;
        });
        event(new TransferCreated($transfer));

        return new TransferResource($transfer);
    }


    /**
     * change Status.
     *
     * @param  \App\Models\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request, Transfer $transfer)
    {
        $transfer->status = $request->status;
        $transfer->save();
        if($request->status == 'completed'){

            $bankCode   = $transfer->user->bank ? $transfer->user->bank->code : '-';
            $bankNumber = substr($transfer->user->iban_number, -4);
    
            $transaction = new Transaction;
            $transaction->user_id     = $transfer->user_id;
            $transaction->type        = 'debit';
            $transaction->amount      = $transfer->amount;
            $transaction->reference   = $transfer->id;
            $transaction->description = 'Transfer - ' . $bankCode . ' XXXX' . $bankNumber;
            $transaction->transaction_source = 'transfer';
            $transaction->save();

            $bills = $transfer->bills;
            $user_id = $transfer->user_id;
            foreach ($bills as $bill) {
                if($bill->user_id == $user_id){
                    $bill->settled = true;
                }

                if($bill->isHaveChannelOwenByUser($user_id)){
                   $bill->channel_settled = true; 
                }
                $bill->save();
            }
            event(new TransferCreated($transfer));
        }
        return new TransferResource($transfer);
    }

        /**
     * send Mails.
     *
     * @return void
     */
    protected function sendMails($emails_string, $date, $transfer)
    {
        $emails = explode(",", $emails_string);
        if(count($emails)){
            foreach ($emails as $email) {
                Mail::to($email)->send(new RequestTransferMail($date, auth()->user(), $transfer));
            }
        }
    }

}
