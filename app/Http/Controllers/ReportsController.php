<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MerchantsOutstandingStoreRequest;
use App\Models\Report;
use App\Models\User;
use App\Events\GenerateReport;
use Carbon\Carbon;


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
            'payment_methods' => [
                'all' => 'All',
                'cash' => 'Cash',
                'online' => 'Online',
                'payment_machine' => 'Payment Machine', 
            ],
            'sources' => [
                'all' => 'All',
                'sure_bill' => 'Sure Bill',
                'pos' => 'POS',
                'api' => 'API',
            ]
        ];

        $data['payments'] = User::payments();

        return view('reports.payment-record', $data);
    }
}
