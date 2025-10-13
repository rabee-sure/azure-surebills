<?php

namespace App\Nova;

use App\Nova\Actions\TranferBillsExcelDownload;
use App\Nova\Actions\TranferTransactionsExcelDownload;
use PosLifestyle\DateRangeFilter\DateRangeFilter;
use App\Nova\Filters\UserName;
use App\Nova\Filters\TransferStatus;
use App\Nova\Metrics\TotalCommissions;
use App\Nova\Metrics\TotalDue;
use App\Nova\Metrics\TotalIncome;
use App\Nova\Metrics\TotalPaid;
use App\Nova\Metrics\TotalVatOnCommissions;
use App\Rules\TransferBalance;
use App\Rules\ValidateUploadFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inspheric\Fields\Indicator;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Titasgailius\SearchRelations\SearchesRelations;
use Ebess\AdvancedNovaMediaLibrary\Fields\Files;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class Transfer extends Resource
{
    use SearchesRelations;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Transfer::class;

    public static $displayInNavigation = false;

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Transfers');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Transfer');
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * order By.
     *
     * @var array
     */
    public static $orderBy = [
        'created_at' => 'desc'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make(__('Merchant ID'), 'user_id'),

            $request->user()->can('show merchants') ? BelongsTo::make(__('Business Name'), 'user', User::class)->displayUsing(function($user){return $user->business_name_en ? $user->business_name_en : $user->mainStoreUser->business_name_en;})->searchable() : Text::make(__('Business Name'), function(){return $this->user->business_name_en ? $this->user->business_name_en : $this->user->mainStoreUser->business_name_en;}),

            Number::make(__('Amount'), 'amount')
                ->min(1)
                ->step(0.1)
                ->rules('required', new TransferBalance($request->viaResourceId)),

            Number::make(__('Transfer Fees'), 'transfer_fees')
                ->min(1)
                ->step(0.1)
                ->rules('required'),

            Number::make(__('Net Amount'), 'net_amount')
                ->min(1)
                ->step(0.1)
                ->rules('required'),

            Textarea::make(__('Note'), 'note'),

            File::make(__('Attachment'), 'attachment')->disk('public')->rules(new ValidateUploadFile(['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xlsx', 'csv'])),

            Files::make('Excel File', 'transfers_transactions')->hideFromIndex()->hideFromDetail(),

            // Custom version that mimics Nova gallery but removes the eye icon
            Text::make(__('Excel File'), function () {
                $mediaItems = $this->getMedia('transfers_transactions');

                if ($mediaItems->isEmpty()) {
                    return '—';
                }

                $items = $mediaItems->map(function ($media) {
                    $downloadUrl = "/nova-vendor/ebess/advanced-nova-media-library/download/{$media->id}";
                    $fileName = e($media->file_name);

                    return <<<HTML
                    <div class="gallery-item gallery-item-file mb-3 p-3 mr-3" 
                        style="display:inline-block; border:1px solid #e5e7eb; border-radius:8px;">
                        <div class="gallery-item-info" style="display:flex; align-items:center; gap:8px;">
                            <a href="{$downloadUrl}" class="download mr-2" title="Download {$fileName}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" 
                                    viewBox="0 0 20 22" aria-labelledby="download" role="presentation" 
                                    class="fill-current text-primary">
                                    <path d="M11 14.59V3a1 1 0 0 1 2 0v11.59l3.3-3.3a1 1 0 0 1 1.4 1.42l-5 5a1 1 0 0 1-1.4 0l-5-5a1 1 0 0 1 1.4-1.42l3.3 3.3zM3 17a1 1 0 0 1 2 0v3h14v-3a1 1 0 0 1 2 0v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3z"></path>
                                </svg>
                            </a>
                            <span class="label" style="font-size:13px;">{$fileName}</span>
                        </div>
                    </div>
                    HTML;
                })->implode('');

                return "<div class='gallery'><div class='gallery-list clearfix'>{$items}</div></div>";
            })
            ->onlyOnDetail()
            ->asHtml(),

            Text::make(__('Cycle Date'))->displayUsing(function(){
                if(isset($this->filters['date'])){
                    $to = (isset($this->filters['date']['to'])) ? Carbon::parse($this->filters['date']['to'])->toDateString(): null;
                    $cycle_date = (isset($this->filters['date']['cycle_date'])) ? Carbon::parse($this->filters['date']['cycle_date'])->toDateString(): '-';
                    return  $to ?? $cycle_date;
                }
                return '-';

            }),

            DateTime::make(__('Created At'), 'created_at')->exceptOnForms(),

            Indicator::make(__('Status'), 'status')
                ->labels([
                    'pending' => __('pending transfer'),
                    'send_to_sps' => __('Send To SPS'),
                    'completed' => __('completed transfer'),
                    'failed' => __('Failed'),
                    'canceled' => __('canceled'),
                ])
                ->colors([
                    'canceled' => 'grey',
                    'completed' => 'green',
                    'send_to_sps' => 'blue',
                    'failed' => 'red',
                    'pending' => 'warning',
                ]),

            // BelongsToMany::make(__('Bills'), 'bills', Bill::class),
            BelongsToMany::make(__('Transactions'), 'transactions', Transaction::class),
            HasMany::make(__('Logs'), 'logs', TransferLog::class),
        ];
    }

    public static $searchRelations = [
        'user' => ['name'],
    ];

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            // (new TotalIncome)->width('1/5'),
            // (new TotalCommissions)->width('1/5'),
            // (new TotalVatOnCommissions)->width('1/5'),
            // (new TotalPaid)->width('1/5'),
            // (new TotalDue)->width('1/5'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new DateRangeFilter(__('Date Range'), 'created_at'),
            new UserName(),
            new TransferStatus(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    public function actions(Request $request)
    {
        return [
            // (new TranferBillsExcelDownload)
            //     ->onlyOnDetail()
            //     ->canRun(function(NovaRequest $request) {
            //         return TRUE;
            //     }),
            (new TranferTransactionsExcelDownload)
                ->onlyOnDetail()
                ->canRun(function(NovaRequest $request) {
                    return TRUE;
                }),
                (new DownloadExcel)->withHeadings()->withName(__('download excel')),
        ];
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }
}
