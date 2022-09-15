<?php

namespace App\Console\Commands;

use App\Exports\BillsDataExport;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExportBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:export {--status=} {--application_id=} {--created_at_start=} {--created_at_end=} {--paid_at_start=} {--paid_at_end=} {--refunded_at_start=} {--refunded_at_end=} {--user_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export bills to excel';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $queryFilter = [
            'status' => $this->option('status'),
            'application_id' => $this->option('application_id'),
            'created_at' => ($this->option('created_at_start') != null && $this->option('created_at_end') != null) ? [$this->option('created_at_start'),$this->option('created_at_end')] : null,
            'paid_at' => ($this->option('paid_at_start') != null && $this->option('paid_at_end') != null) ? [$this->option('paid_at_start'),$this->option('paid_at_end')] : null,
            'refunded_at' => ($this->option('refunded_at_start') != null && $this->option('refunded_at_end') != null) ?[$this->option('refunded_at_start'),$this->option('refunded_at_end')] : null,
            'user_id' => $this->option('user_id'),
        ];
        
        $file_name = 'bills_'.Carbon::now()->timestamp.'.xlsx';
        (new BillsDataExport($queryFilter))
        ->store($filePath = 'shared-bills/'. $file_name, 'local');
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
}
