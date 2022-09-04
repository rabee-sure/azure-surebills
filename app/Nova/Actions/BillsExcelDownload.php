<?php

namespace App\Nova\Actions;

use App\Exports\BillsDataExport;
use App\Exports\BillsExport;
use App\Http\Resources\BillResource;
use App\Jobs\SendExportedBillsMailsJob;
use App\Mail\BillsExportedExcelMail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Maatwebsite\Excel\Facades\Excel;

class BillsExcelDownload extends Action
{

    public static $chunkCount = 1000000;

    public $email;
    public $filters;

    public function __construct($email, $filters)
    {
        $this->email = $email;
        $this->filters = $filters;
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        $queryFilter = self::rebuildFilter(json_decode(base64_decode($this->filters['filters'])));

        $file_name = 'bills_'.Carbon::now()->timestamp.'.xlsx';
        (new BillsDataExport($queryFilter))
        ->store($filePath = 'shared-bills/'. $file_name)
        ->chain([
            (new SendExportedBillsMailsJob($file_name, $this->email))
        ]);
        
        return Action::message('Exported file will send to your email after finished!');
    }

    protected function rebuildFilter($decodedFilter){
        $FilterdColums = [];
        foreach($decodedFilter as $filter){
            switch ($filter->class) {
                case 'App\Nova\Filters\BillStatus':
                    $FilterdColums['status'] = $filter->value;
                    break;

                case 'App\Nova\Filters\BillSource':
                    $FilterdColums['application_id'] = $filter->value;
                    break;

                case 'PosLifestyle\DateRangeFilter\DateRangeFilter_created_at':
                    $FilterdColums['created_at'] = $filter->value;
                    break;

                case 'PosLifestyle\DateRangeFilter\DateRangeFilter_paid_at':
                    $FilterdColums['paid_at'] = $filter->value;
                    break;

                case 'PosLifestyle\DateRangeFilter\DateRangeFilter_refunded_at':
                    $FilterdColums['refunded_at'] = $filter->value;
                    break;

                case 'App\Nova\Filters\UserId':
                    $FilterdColums['user_id'] = $filter->value;
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        return $FilterdColums;
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}