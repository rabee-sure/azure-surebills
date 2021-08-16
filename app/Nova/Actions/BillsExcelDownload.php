<?php

namespace App\Nova\Actions;

use Carbon\Carbon;
use App\Exports\BillsExport;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use App\Http\Resources\BillResource;
use Maatwebsite\Excel\Facades\Excel;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BillsExcelDownload extends Action
{

    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return  __('Download Bills Excel');
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
        $file_name = 'bills/'.Carbon::now()->timestamp.'.xlsx';
        $data = json_decode((BillResource::collection($models))->toJson(), true);
        Excel::store(new BillsExport($data), $file_name);

        $new_file_name = 'public/shared-bills/'.$file_name;
        Storage::delete( $new_file_name );
        Storage::copy( $file_name, $new_file_name );
        $path = storage_path('app/'.$new_file_name);
        if(\File::exists($path)){
            return Action::download( Storage::url($new_file_name), $file_name);
        }
        else{
        dd('dd');
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
}