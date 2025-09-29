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

            File::make(__('Attachment'), 'attachment')->rules(new ValidateUploadFile(['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xlsx', 'csv'])),

            Files::make('Excel File', 'transfers_transactions'),

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
