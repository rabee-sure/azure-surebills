<?php

namespace App\Nova\Actions;

use App\Services\TransferService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class TranferTransactionsExcelDownload extends Action
{    
    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return  __('Download Transfer Transactions Excel');
    }
    
    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $transfer) {
            $transfer_files = $transfer->filters['files']??[];
            $transactions_file_path = $transfer_files['file_path'] ?? 'rfedw';
            $path = storage_path('app/public/' . $transactions_file_path);

            if (!File::exists($path)) {
                $file_name = TransferService::saveExcelFileName($transfer);
                TransferService::createTransactionsExcel($transfer, $file_name);
                $transfer->refresh();
                $path = storage_path('app/public/' . $transfer->filters['files']['file_path']);
            }

          
            if(File::exists($path)){
                return Action::download( Storage::url('public/'.$transfer->filters['files']['file_path']), $transfer->filters['files']['file_name']);
            }
            else
                return Action::danger(404);
        }
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
    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function updateFile($model)
    {
        $filename = 'bills/'.$model->filters['files']['folder'].'/';
        $filename = $filename . str_replace('transactions-', '', $model->filters['files']['transactions']);
        TransferService::createTransactionsExcel( $model->transactions->load('bill'), $filename);
    }


}