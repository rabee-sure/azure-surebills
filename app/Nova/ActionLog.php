<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Panel;
use App\Nova\Filters\ActionByUserFilter;
use App\Nova\Filters\ActionTypeFilter;
use App\Nova\Filters\DateRange;
use Laravel\Nova\Http\Requests\NovaRequest;

class ActionLog extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\ActionLog::class;

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Actions Logs');
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
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('ID', 'id')->sortable(),
            $request->user()->can('show system admins') ? BelongsTo::make('User', 'Admin', 'App\Nova\Admin') : Text::make(__('User'), function(){return $this->Admin->name;}),
            BelongsTo::make('Action', 'SystemAction', 'App\Nova\SystemAction')->display(function () {
                return __('action_names.'.$this->SystemAction->action_name);
            }),
            Text::make('Message', 'message'),
            DateTime::make('Created at', 'created_at'),
            new Panel(__('Changes'), $this->ChangesFields()),
        ];
    }

    protected function ChangesFields()
    {
        $payload = json_decode($this->payload,true);

        $panelFields = [];

        $panelFields[] = Text::make('Object', function () {
            return __('models_class.'.$this->model_class);
        });
        $panelFields[] = Text::make('Object ID', 'model_id');

        if(!empty($payload)){
            foreach($payload as $fKey => $field){

                $old_value = $this->getValue(substr(strrchr($this->model_class, "\\"), 1), $fKey, $field['old_value']);
                $new_value = $this->getValue(substr(strrchr($this->model_class, "\\"), 1), $fKey, $field['new_value']);

                $panelFields[] = Text::make($fKey, function () use ($old_value, $new_value) {
                    return 'changed from <span style="color:red;">'.$old_value.'</span> to <span style="color:green;">'.$new_value.'</span>';
                })->asHtml()->onlyOnDetail();
            }
        }
        return $panelFields;
    }

    public function getValue($model, $field, $value){
        if(is_bool($value)){
            $value = $value ? 1 : 0;
        }
        $readableValue = config('ResourcesFields.'.$model.'.'.$field.'.'.$value);
        if($readableValue != null){
            $value = $readableValue;
        }

        return $value;
    }

    public function authorizedToView(Request $request)
    {
        return false;
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
        return [
            new ActionByUserFilter,
            new ActionTypeFilter,
            new DateRange,
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
