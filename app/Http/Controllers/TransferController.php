<?php

namespace App\Http\Controllers;

use App\Events\TransferCreated;
use App\Http\Resources\TransferResource;
use App\Mail\RequestTransferMail;
use App\Models\Bank;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
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
    public function request(Request $request)
    {
        $user = auth()->user();
        $to = Carbon::now()->startOfDay();
        // $from = TransferService::getFromDate($user);
        $from = $user->created_at;

        $file_name = $this->getExcelFileName($user, $to);
        $bills = TransferService::getbillsBetweenDate($from, $to, $user, $file_name);

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

        $bank = $user->bank;
        $transfer_fees = $bank->fees+ ($bank->fees * 0.15);
        $data = [
            'from' => $from,
            'to' => $to,
            'transfer_fees' => $transfer_fees,
            'note' => '',
            'created_by_id' => null,
            'bank_id' => $bank->id,
            'user_id' => $user->id,
            'iban_number' => $user->iban_number,
            'beneficiary_name' => $user->beneficiary_name,
        ];
        
        $transfer = TransferService::makeTransfer('pending', $amount, $bills, $data);

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
        $toDate = new Carbon($request->to);
        $fromDate = $fromDate->addHours(2);
        $toDate = $toDate->addHours(2);
        if($request->from == $request->to){
            $fromDate = $fromDate->startOfDay();
            $toDate = $toDate->endOfDay();
        }
        
        $bills = Bill::whereIn('id', $request->bills_ids)->get();
        $user = User::find($request->user_id);
        $amount = TransferService::getAmount($bills, $user);
        if($bills->where('pending_settled', true)->count() != 0 || $bills->where('settled', true)->count() != 0){
            return response()->json(['error' => __('Bills duplicate in another transfer')], 422);

        }elseif($bills->whereNotNull('refunded_at')->count() != 0 ){
            return response()->json(['error' => __('Bills have refunded bill')], 422);
        }elseif($amount <= 0 ){
            return response()->json(['error' => __('balance must be greater than 0')], 422);
        } else{

            $bank = Bank::find($request->bank_id);
            $transfer_fees = $bank->fees + ($bank->fees * 0.15);
            $status = $request->get('status', 'pending');

            $data = [
                'from' => $fromDate,
                'to' => $toDate,
                'transfer_fees' => $transfer_fees,

                'user_id' => $user->id,
                'note' => $request->note,
                'attachment' => $request->attachment,
                'created_by_id' => auth()->user()->id,
                'bank_id' => $user->bank_id,
                'iban_number' => $user->iban_number,
                'beneficiary_name' => $user->beneficiary_name,
            ];
            
            $transfer = TransferService::makeTransfer($status, $amount, $bills, $data);

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

        $bills = $transfer->bills;
        $user_id = $transfer->user_id;
        foreach ($bills as $bill) {
            $bill->pending_settled = false; 
            $bill->save();
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
