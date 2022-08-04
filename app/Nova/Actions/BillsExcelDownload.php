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
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Maatwebsite\Excel\Facades\Excel;

class BillsExcelDownload extends Action implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public static $chunkCount = 1000000;

    public function handle(ActionFields $fields, Collection $models)
    {
        $file_name = 'bills_'.Carbon::now()->timestamp.'.xlsx';
        $data = json_decode((BillResource::collection($models->load('application')))->toJson(), true);

        (new BillsExport($data))->store($filePath = 'shared-bills/'. $file_name);
        $message = (new BillsExportedExcelMail($file_name));
        Mail::to([Auth::user()->email, 'mzain@sure.com.sa'])->queue($message);
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