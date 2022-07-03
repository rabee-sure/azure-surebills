<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MerchantsOutstandingStoreRequest;
use App\Models\Report;
use App\Models\User;
use App\Events\GenerateReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentRecordExport;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show payment record', ['only' => ['paymentRecord']]);
    }

    public function index(Request $request)
    {
        return view('reports.index');
    }

    public function merchants_outstanding(Request $request)
    {
        $data['merchants'] = User::select('id', 'business_name_en', 'business_name_ar')->whereNull('store_main_user_id')->get();
        $data['requests'] = Report::all()->sortByDesc('created_at')->paginate(10);
        return view('reports.merchants-outstanding', $data);
    }

    public function merchants_outstanding_store(MerchantsOutstandingStoreRequest $request)
    {
        $dateRange = explode(' - ', $request->dates);
        $from = date('Y-m-d', strtotime($dateRange[0]));
        $to = date('Y-m-d', strtotime($dateRange[1]));
        
        $paramsArr = array();
        $paramsArr['merchants'] = implode('","', $request->merchants);
        $paramsArr['from'] = $from;
        $paramsArr['to'] = $to;

        $name = 'merchants-outstanding';
        $params = json_encode($paramsArr, true);
        $emails = $request->emails;

        $report = Report::create([
            'name' => $name,
            'params' => $params,
            'emails' => $emails,
        ]);

        GenerateReport::dispatch($report->id);
        
        return redirect()->route('reports.merchants-outstanding');
    }

    public function paymentRecord(Request $request)
    {
        $data['filters'] = [
            'transaction_types' => [
                'all' => 'All',
                'debit' => 'Debit',
                'credit' => 'Credit',
            ],
            'payment_ways' => [
                'all' => 'All',
                'cash' => 'Cash',
                'online' => 'Online',
                'payment_machine' => 'Payment Machine', 
                'bank_transfer' => 'Bank Transfer', 
            ],
            'sources' => [
                'all' => 'All',
                'sure_bill' => 'Sure Bill',
                'pos' => 'POS',
                'api' => 'API',
            ]
        ];

        $user = Auth::user()->store_main_user_id ? User::find(Auth::user()->store_main_user_id) : Auth::user();

        $query = $user->paymentRecordQuery($request);

        $allQuery = $query->get();
        $paginatedQuery = $query->paginate(100);
        
        $data['payments'] = $paginatedQuery;

        $credit = $allQuery->where('transaction_source', 'bill')->sum('amount');
        $debit = $allQuery->where('transaction_source', 'refund')->sum('amount');
        $data['total'] = $credit - $debit;

        return view('reports.payment-record', $data);
    }

    public function paymentRecordExport(Request $request)
    {
        return Excel::download(new PaymentRecordExport($request), 'payment_records.xlsx');
    }
}
