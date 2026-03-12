<?php

namespace App\Nova;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Http\Request;
use Kpolicar\DateRange\DateRange;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use OptimistDigital\MultiselectField\Multiselect;
use App\Events\AddActionLogEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BillReport extends Resource
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
    public static $search = [
        'id',
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
            ID::make(__('ID'), 'id')->sortable(),

            Hidden::make('type')->default('bill'),

            Multiselect::make(__('Merchants'), 'merchants')->options(self::merchants())->onlyOnForms(),
            Multiselect::make(__('Channels'), 'channels')->options(self::merchantChannels())->onlyOnForms(),

            Text::make(__('Paid Period'), function(){
                return __('From :from To :to', ['from' => $this->parameters["paid_from"], 'to' => $this->parameters["paid_to"]]);
            })->exceptOnForms(),

            DateRange::make(__('Paid Period'), ['from', 'to'])->onlyOnForms()->rules('required'),

            Text::make(__('Emails'), 'emails')->rules('required'),

            Date::make(__('Request date'), 'created_at')->exceptOnForms(),

            Text::make(__('Download File'), function () {

                if (!$this->name) {
                    return "<span style='color: #aaa;'>-</span>";
                }

                $path = 'reports/'.$this->name.'/'.$this->name.'_'.$this->id.'.xlsx';

                if (!Storage::disk('oci')->exists($path)) {
                    return "<span style='color: #aaa;'>File not found</span>";
                }

                $url = Storage::disk('oci')->temporaryUrl(
                    $path,
                    now()->addMinutes(10)
                );

                return "<a class='btn btn-success' style='margin:5px' href='{$url}' target='_blank'>
                            <i class='fa fa-download'></i>
                        </a>";
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
        $merchantsOptions = [null => __('All')];
        $merchantes = User::whereNull('store_main_user_id')->get();
        foreach($merchantes as $merchante)
        {
            $merchantsOptions[$merchante->id] = $merchante->name;
        }
        return $merchantsOptions;
    }

    private function merchantChannels()
    {
        $merchantChannelsOptions = [];
        $channels = Channel::get();
        foreach($channels as $channel)
        {
            $merchantChannelsOptions[$channel->id] = $channel->name;
        }
        return $merchantChannelsOptions;
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('type', 'bill');
    }
    public static function authorizedToCreate(Request $request)
    {
        return auth()->user()->can('create bills report');
    }
    public function authorizedToView(Request $request)
    {
        return auth()->user()->can('show bills report');
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
