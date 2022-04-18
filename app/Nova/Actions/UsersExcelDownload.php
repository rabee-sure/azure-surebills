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
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class UsersExcelDownload extends DownloadExcel implements WithMapping
{
    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return  __('Download Users Excel');
    }

    public function headings(): array
    {
        return['ID', __('Balance'), __('Business Name'), __('Bank'), __('Iban Number'), __('Account Name'), __('Verified')];
    }

    public function map($user): array
    {
        if($user->store_main_user_id)
        {
            $user->bank = $user->mainStoreUser->bank;
            $user->iban_number = $user->mainStoreUser->iban_number;
        }
        return [
            $user->id,
            $user->balance_string,
            $user->mainStoreUser ? $user->mainStoreUser->business_name_en : $user->business_name_en,
            $user->bank->name?? '',
            $user->iban_number,
            $user->name,
            $user->verify_status,
        ];
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
