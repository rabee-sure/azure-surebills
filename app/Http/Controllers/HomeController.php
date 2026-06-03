<?php

namespace App\Http\Controllers;

use App\Events\ContactSendEmail;
use App\Http\Requests\ContactRequest;
use App\Models\Bill;
use App\Models\RefundedBill;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function landing()
    {
        if (auth()->user()) {
            return redirect('/home');
        }

        return view('landing/home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(auth()->user()->source == 'pos')
        {
            return redirect(route('reports.paymentRecord'));
        }

        $user = auth()->user();
        $user->userId = auth()->user()->store_main_user_id ?? auth()->user()->id;
        $bills = Bill::userId(auth()->user()->store_main_user_id ?? auth()->user()->id);

        // Create union query with refunded bills
        $latestQuery = clone $bills;
        $latestQuery = $latestQuery->select([
            'id',
            'number',
            'customer_name',
            'fixed_total',
            'status',
            'payment_way as method',
            'created_at',
            'user_id',
            DB::raw("CASE WHEN debit_note_bill_id IS NULL THEN 'bill' ELSE 'debit_note' END as type")
        ])->union(
            RefundedBill::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)
            ->select([
                'id',
                'number',
                'customer_name',
                'amount as fixed_total',
                'status',
                'method',
                'created_at',
                'user_id',
                DB::raw("'credit_note' as type")
            ])
        );

        $latest = $latestQuery->orderBy('created_at', 'desc')->take(6)->get();

        $balance = $user->balance;

        $total_paid_query = Transaction::whereNotNull('bill_id')->where('user_id', auth()->user()->store_main_user_id ?? auth()->user()->id)->whereHas('bill', function($q){
            $q->where('payment_way', 'online');
        });

        $total_paid = $total_paid_query->where('type', 'credit')->sum('amount') - $total_paid_query->where('type', 'debit')->sum('amount');

        $total_bills_query = clone $bills;
        $total_bills = $total_bills_query->count();

        $total_paid_bills_query = clone $bills;
        $total_paid_bills = $total_paid_bills_query->where('status', 'paid')->count();

        return view('home', [
            'user' =>  $user,
            'balance' =>  $balance ?? 0,
            'latest' =>  $latest,
            'total_paid' =>  $total_paid,
            'total_bills' =>  $total_bills,
            'total_paid_bills' =>  $total_paid_bills,
        ]);
    }

    /**
     * Show contact page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function contact()
    {
        return view('landing/contact');
    }

    public function contactSendForm(ContactRequest $request){
        $data['source'] = $request->source;
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['company'] = $request->company;
        $data['mobile'] = $request->mobile;
        $data['message'] = $request->message;
        //fire event to send email for message
        event(new ContactSendEmail($data));

        return response()->json(['success' => __('message will sent to support team')], 200);
    }

    /**
     * Show privacy page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function privacy()
    {
        return view('landing/privacy_policy');
    }

    /**
     * Show terms page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function terms()
    {
        return view('landing/terms');
    }
}
