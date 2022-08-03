<?php

namespace App\Nova\Actions;

use App\Exports\BillsExport;
use App\Http\Resources\BillResource;
use App\Mail\BillsExportedExcelMail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;

class BillsExcelDownload extends Action implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public static $chunkCount = 1000000;

    public function handle(ActionFields $fields, Collection $models)
    {
        $file_name = 'bills_'.Carbon::now()->timestamp.'.xlsx';
        $data = json_decode((BillResource::collection($models->load('application')))->toJson(), true);

        (new BillsExport($data))
        ->store($filePath = 'public/shared-bills/'. $file_name)
        ->chain([
            $message = (new BillsExportedExcelMail($file_name))->onQueue(env('EMAILS_QUEUE')),
            Mail::to(Auth::user()->email)->queue($message)
        ]);

        // $new_file_name = 'public/shared-bills/'.$file_name;
        // Storage::delete( $new_file_name );
        // Storage::copy( $file_name, $new_file_name );
        // $path = storage_path('app/'.$new_file_name);
        // if(\File::exists($path)){
        //     return Action::download( Storage::url($new_file_name), $file_name);
        // }
        // else{
        //     return Action::danger(404);
        // }
        return Action::message("Exported Bills file will send to your mail!");
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