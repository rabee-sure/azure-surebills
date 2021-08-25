<?php

namespace App\Nova;

use App\Nova\Actions\TranferBillsExcelDownload;
use App\Nova\Actions\TranferTransactionsExcelDownload;
use App\Nova\Filters\DateRange;
use App\Nova\Filters\UserName;
use App\Nova\Metrics\TotalCommissions;
use App\Nova\Metrics\TotalDue;
use App\Nova\Metrics\TotalIncome;
use App\Nova\Metrics\TotalPaid;
use App\Nova\Metrics\TotalVatOnCommissions;
use App\Rules\TransferLogBalance;
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
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Titasgailius\SearchRelations\SearchesRelations;

class TransferLog extends Resource
{
    use SearchesRelations;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\TransferLog::class;

    public static $displayInNavigation = false;

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Transfer Logs');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Transfer Log');
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
            BelongsTo::make(__('User'), 'user', User::class)->searchable(),
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
            new DateRange(),
            new UserName(),
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
