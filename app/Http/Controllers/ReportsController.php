<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MerchantsOutstandingStoreRequest;
use App\Models\Report;
use App\Models\User;
use App\Jobs\CreateReportExcelFileJob;
use App\Events\GenerateReport;


class ReportsController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.index');
    }

    public function merchants_outstanding(Request $request)
    {
        $data['merchants'] = User::select('id', 'business_name_en')->whereNull('store_main_user_id')->get();
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
}
