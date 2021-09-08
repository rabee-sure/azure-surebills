<?php

namespace App\Http\Controllers;

use App\Events\TransferCreated;
use App\Http\Resources\TransferResource;
use App\Mail\RequestTransferMail;
use App\Models\Bank;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\TransferLog;
use App\Models\User;
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
        $this->authorize('viewBills', $transfer);
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
    public function transactions(Transfer $transfer, Request $request)
    {
        $this->authorize('viewTransactions', $transfer);
        $transactions = $transfer->transactions;
        $totals['debit'] = round2($transactions->where('type', 'debit')->sum('amount'));
        $totals['credit'] = round2($transactions->where('type', 'credit')->sum('amount'));
        $totals['all'] = round2($totals['credit'] - $totals['debit']);   
        return view('transfers.transactions', [
            'transfer' => $transfer,
            'transactions' => $transactions,
            'totals' => $totals,
        ]);
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function request(Request $request)
    {
        $user = auth()->user();
        $cycleDate = Carbon::now()->addHours(3);
        // $from = $user->created_at;

        $file_name = $this->getExcelFileName($user, $cycleDate);
        $transactions = TransferService::getTransactionsByCycleDate($cycleDate, $user, $file_name);

        $amount = TransferService::getAmount($transactions, $user);
        $settings =  Valuestore::make(storage_path('app/settings.json'));

        $transfer_minimum = $settings->get('transfer_minimum');
        $transfer_emails = $settings->get('transfer_emails');

        if ($transfer_minimum > $amount) {
            return redirect()->back()->withErrors([__('Your balance is not allowed to transfer. The minimum transfer balance is :minimum', ['minimum'=>$transfer_minimum])]);
        }

        if ($user->transfers->where('status', 'pending')->count()) {
            return redirect()->back()->withErrors([__('Sorry, you cannot request a transfer now. Please wait for the Transfer of the previous transfer')]);
        }

        $bank = $user->bank;
        $transfer_fees = $bank->fees+ ($bank->fees * 0.15);
        // dd([$from, $to ]);
        $data = [
            'cycle_date' => $cycleDate,
            'transfer_fees' => $transfer_fees,
            'note' => '',
            'created_by_id' => null,
            'bank_id' => $bank->id,
            'user_id' => $user->id,
            'iban_number' => $user->iban_number,
            'beneficiary_name' => $user->beneficiary_name,
            'file_name' => $file_name,
        ];
        
        $transfer = TransferService::makeTransfer('pending', $amount, $transactions, $data);

        if($transfer)
            $this->sendMails($transfer_emails, $cycleDate, $transfer);
        
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::find($request->user_id);

        $cycleDate = new Carbon($request->cycle_date);
        $cycleDate = $cycleDate->addHours(3);

        $transactions = Transaction::whereIn('id', $request->transactions_ids)->get();
        $file_name = $this->getExcelFileName($user, $cycleDate);
        TransferService::createTransactionsExcel($transactions, $file_name);

        $amount = TransferService::getAmount($transactions, $user);
        
        if($transactions->where('pending_settled', true)->count() != 0 || $transactions->where('settled', true)->count() != 0){
            return response()->json(['error' => __('Bills duplicate in another transfer')], 422);

        }if($amount <= 0 ){
            return response()->json(['error' => __('amount must be greater than 0')], 422);
        }elseif($amount > $user->balance){
            return response()->json(['error' => __("Quantity must be less than or equal to the user's balance")], 422);
        } else{

            $bank = Bank::find($request->bank_id);
            $transfer_fees = ($bank) ? $bank->fees + ($bank->fees * 0.15): 0;
            $status = $request->get('status', 'pending');

            $data = [
                'cycle_date' => $cycleDate,
                'transfer_fees' => $transfer_fees,

                'user_id' => $user->id,
                'note' => $request->note,
                'attachment' => $request->attachment,
                'created_by_id' => auth()->user()->id,
                'bank_id' => $user->bank_id,
                'iban_number' => $user->iban_number,
                'beneficiary_name' => $user->beneficiary_name,
                'file_name' => $file_name,
            ];
            
            $transfer = TransferService::makeTransfer($status, $amount, $transactions, $data);

            event(new TransferCreated($transfer));

            return new TransferResource($transfer);
        }
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

         $log = TransferLog::create([
            'type' => $request->status.' transfer',
            'user_id' => auth()->user()->id,
            'transfer_id' => $transfer->id,
            'transfer_status' => $transfer->status,
        ]);

        if($request->status == 'completed'){

            TransferService::createTransferTransaction($transfer);

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
     * change Status.
     *
     * @param  \App\Models\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, Transfer $transfer)
    {
        $transfer->status = 'canceled';
        $transfer->save();
        $user_id = $transfer->user_id;

        $bills = $transfer->bills;
        foreach ($bills as $bill) {
            $bill->pending_settled = false; 
            $bill->save();
        }

        $transactions = $transfer->transactions;
        foreach ($transactions as $transaction) {
            $transaction->pending_settled = false; 
            $transaction->save();
        }

         $log = TransferLog::create([
            'type' => 'cancel transfer',
            'user_id' => auth()->user()->id,
            'transfer_id' => $transfer->id,
            'transfer_status' => $transfer->status,
        ]);

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

    /**
     * get Excel File Name.
     *
     * @return String
     */
    protected function getExcelFileName($user, $to)
    {
        return "bills/$user->business_name_slug/{$to->timestamp}_sure_bills_request_transfer.xlsx";
    }    
}
