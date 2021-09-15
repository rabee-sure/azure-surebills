<?php

namespace App\Nova\Actions;

use App\Services\TransferService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
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
        foreach ($models as $model) {
            $this->updateFile($model);
            $filename = 'bills/'.$model->filters['files']['folder'].'/'.$model->filters['files']['transactions'];

            $new_file_name = 'public/shared-bills/'.$model->filters['files']['transactions'];
            Storage::delete( $new_file_name );
            Storage::copy( $filename, $new_file_name );
            $path = storage_path('app/'.$new_file_name);
            if(\File::exists($path)){
                return Action::download( Storage::url($new_file_name), $model->filters['files']['transactions']);
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