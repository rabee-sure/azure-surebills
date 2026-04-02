<?php

namespace App\Nova\Actions;

use App\Exports\MerchantsDataExport;
use App\Jobs\SendExportedMercahntsReportMailsJob;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class MerchantsExcelDownload extends Action
{

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

        $file_name = 'merchants_report_'.Carbon::now()->timestamp.'.xlsx';
        (new MerchantsDataExport($queryFilter))
        ->store($filePath = 'merchants_reports/'. $file_name, 'oci')
        ->chain([
            (new SendExportedMercahntsReportMailsJob($file_name, $this->email))
        ]);

        return Action::message('Exported file will send to your email after finished!');
    }

    protected function rebuildFilter($decodedFilter){
        $FilterdColums = [];
        foreach($decodedFilter as $filter){
            switch ($filter->class) {
                case 'App\Nova\Filters\YearFilter':
                    $FilterdColums['created_at'] = $filter->value;
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
