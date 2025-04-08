<?php

namespace App\Nova;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Kpolicar\DateRange\DateRange;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use OptimistDigital\MultiselectField\Multiselect;
use App\Events\AddActionLogEvent;
use Illuminate\Support\Facades\Auth;

class MerchantsOutstandingReport extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */

    public static $model = \App\Models\Report::class;
    public static $displayInNavigation = false;

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
    public static $search = [];

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

            Hidden::make('type')->default('merchants outstanding'),

            Text::make(__('Report Period'), function(){
                return __('From :from To :to', ['from' => $this->parameters["from"], 'to' => $this->parameters["to"]]);
            })->exceptOnForms(),

            Multiselect::make(__('Merchants'), 'merchants')->options(self::merchants())->onlyOnForms()->rules('required'),
            DateRange::make(__('Report Period'), ['from', 'to'])->onlyOnForms(),

            Text::make(__('Emails'), 'emails')->rules('required'),
            Text::make(__('Status'), function(){
                if($this->active == 0)
                {
                    return __('Report Pending');
                }
                else
                {
                    return __('Report Done');
                }
            }),

            Date::make(__('Request date'), 'created_at')->exceptOnForms(),
            Text::make(__('Download File'), function(){
                if(file_exists(storage_path('app/reports/'.$this->name.'/'.$this->name.'_'.$this->id.'.xlsx')))
                {
                    return "<a class='btn btn-success' style='margin:5px' href='". url('file/admins/' . 'reports/'.$this->name.'/'.$this->name.'_'.$this->id.'.xlsx') ."'><i class='fa fa-download' aria-hidden='true'></i></a>";
                }elseif(file_exists(storage_path('app/public/reports/'.$this->name.'/'.$this->name.'_'.$this->id.'.xlsx')))
                {
                    return "<a class='btn btn-success' style='margin:5px' href='". url('file/admins/' . 'public/reports/'.$this->name.'/'.$this->name.'_'.$this->id.'.xlsx') ."'><i class='fa fa-download' aria-hidden='true'></i></a>";
                }
            })->asHtml(),
        ];
    }

    /**
     * Determine if this request is a resource detail request.
     *
     * @return bool
     */
    public function viewIs($view, $request)
    {
        $class = '\Laravel\Nova\Http\Requests\\Resource'.ucfirst($view).'Request';

        return $request instanceof $class;
    }

    private function merchants()
    {
        $merchantsOptions = ['all' => __('All')];
        $merchantes = User::whereNull('store_main_user_id')->get();
        foreach($merchantes as $merchante)
        {
            $merchantsOptions[$merchante->id] = $merchante->name;
        }
        return $merchantsOptions;
    }

    public static function label()
    {
        return 'Merchants Outstanding';
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('type', 'merchants outstanding');
    }

    public static function authorizedToCreate(Request $request)
    {
        return auth()->user()->can('create merchants outstanding report');
    }
    public function authorizedToView(Request $request)
    {
        return auth()->user()->can('show merchants outstanding report');
    }
    public function authorizedToDelete(Request $request)
    {
        return false;
    }
    public function authorizedToUpdate(Request $request)
    {
        return false;
    }
    public static function searchable()
    {
        return false;
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
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

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
