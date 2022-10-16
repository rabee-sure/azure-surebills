<?php

namespace App\Exports;

use App\Http\Resources\BillResource;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use phpDocumentor\Reflection\Types\Null_;

class BillsExport implements FromView, ShouldQueue
{
    use Exportable;
    protected $filter;

    public function __construct($filter)
    {
        $this->filter = $filter;
    }

    /**
    * @return \Illuminate\Support\View
    */
    public function view(): View
    {
        $billsQuery = new Bill;

        if($this->filter['status'] != null){
            $billsQuery = $billsQuery->whereIn('status', $this->filter['status']);
        }


        if($this->filter['application_id'] == 1){
            $billsQuery = $billsQuery->whereNotNull('application_id');
        }elseif($this->filter['application_id'] == 2){
            $billsQuery = $billsQuery->whereNull('application_id');
        }

        if($this->filter['created_at'] != null){
            $from = Carbon::parse($this->filter['created_at'][0])->startOfDay();
            $to = Carbon::parse($this->filter['created_at'][1])->endOfDay();

            $billsQuery = $billsQuery->whereBetween('created_at', [$from, $to]);
        }

        if($this->filter['paid_at'] != null){
            $from = Carbon::parse($this->filter['paid_at'][0])->startOfDay();
            $to = Carbon::parse($this->filter['paid_at'][1])->endOfDay();

            $billsQuery = $billsQuery->whereBetween('paid_at', [$from, $to]);
        }

        if($this->filter['refunded_at'] != null){
            $from = Carbon::parse($this->filter['refunded_at'][0])->startOfDay();
            $to = Carbon::parse($this->filter['refunded_at'][1])->endOfDay();

            $billsQuery = $billsQuery->whereBetween('refunded_at', [$from, $to]);
        }

        if($this->filter['user_id'] != null){
            $billsQuery = $billsQuery->where('user_id', $this->filter['user_id']);
        }

        $bills = $billsQuery->paginate(5000);

        dd($bills);

        $data = json_decode((BillResource::collection($bills->load('application')))->toJson(), true);

        return view('exports.bills', [
            'bills' => $data
        ]);
    }
}
