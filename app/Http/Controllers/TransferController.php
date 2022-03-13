<?php

namespace App\Http\Controllers;

use App\Events\TransferCreated;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\TransferResource;
use App\Mail\RequestTransferMail;
use App\Models\Bank;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\TransferLog;
use App\Models\User;
use App\Services\TransferOperations;
use App\Services\TransferService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Valuestore\Valuestore;

class TransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show transfers');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transfers = auth()->user()->transfers()->with('created_by')->get();
        return view('transfers.index', ['transfers' => $transfers]);
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

        $balance_total = $transfer->transactions()
            ->select(DB::raw("SUM(CASE WHEN type  = 'credit' THEN amount ELSE 0 END) AS credit_total,SUM(CASE WHEN type  = 'debit' THEN amount ELSE 0 END) AS debit_total"))
            ->first();
        $totals['debit'] = round2($balance_total->debit_total);
        $totals['credit'] = round2($balance_total->credit_total);
        $totals['all'] = round2($balance_total->credit_total - $balance_total->debit_total);

        return view('transfers.transactions', [
            'transfer' => $transfer,
            'transactions' => $transfer->transactions()->paginate($request->per_page),
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
        $transfers = Transfer::orderBy('id', 'desc')->pending()->with('created_by', 'user')
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

        $amount = $user->getBalanceBefore($cycleDate->format('Y-m-d'));
        $settings = Valuestore::make(storage_path('app/settings.json'));

        $transfer_minimum = $settings->get('transfer_minimum');
        $transfer_emails = $settings->get('transfer_emails');

        if ($transfer_minimum > $amount) {
            return redirect()->back()->withErrors([__('Your balance is not allowed to transfer. The minimum transfer balance is :minimum', ['minimum'=>$transfer_minimum])]);
        }

        if ($user->transfers->where('status', 'pending')->count()||$user->transfers->where('status', 'send_to_sps')->count()) {
            return redirect()->back()->withErrors([__('Sorry, you cannot request a transfer now. Please wait for the Transfer of the previous transfer')]);
        }

        $bank = $user->bank;
        $transfer_fees = $bank->fees+ ($bank->fees * 0.15);

        $data = [
            'cycle_date' => $cycleDate,
            'transfer_fees' => $transfer_fees,
            'note' => '',
            'created_by_id' => $user->id,
            'bank_id' => $bank->id,
            'user_id' => $user->id,
            'iban_number' => $user->iban_number,
            'beneficiary_name' => $user->beneficiary_name,
        ];

        $transfer = TransferService::makeTransfer('pending', $amount, $data);

        // if($transfer)
            //add listner of transfer file generated
            // $this->sendMails($transfer_emails, $cycleDate, $transfer);

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

        $amount = $user->getBalanceBefore($cycleDate->format('Y-m-d'));

        if($amount <= 0 ){
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
            ];

            $transfer = TransferService::makeTransfer($status, $amount, $data);

            return new TransferResource($transfer);
        }
    }


    /**
     * change Status.
     *
     * @param  \App\Models\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request)
    {
        $transfers = Transfer::whereIn('id', $request->ids )->with('created_by', 'user.bank')->get();

        TransferService::changeTranfersStatus($transfers, $request->status, auth()->user()->id);

        return  TransferResource::collection($transfers);
    }

    /**
     * change Status.
     *
     * @param  \App\Models\Transfer  $Transfer
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, Transfer $transfer)
    {
        $perations = new TransferOperations();
        $perations->cancel([$transfer], 'canceled', auth()->user()->id);

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
     * get userT ransactions with balance by date.
     *
     * @return \Illuminate\Http\Response
     */
    public function userTransactions(Request $request, User $user)
    {
        $transactions = $user->transactions()
            ->amountByCycleDate($request->cycle_date)
            ->orderBy('created_at', 'ASC')
            ->orderBy('order', 'ASC')
            ->orderBy('receipt', 'ASC')
            ->with(['bill.application'])
            ->paginate(10);


        $balance = $user->getBalanceBefore($request->cycle_date);
        return (TransactionResource::collection($transactions))
        ->additional([
            'meta' => [
                'balance' => $balance,
            ]
        ]);
    }
}
