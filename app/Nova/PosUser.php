<?php

namespace App\Nova;

use App\Rules\ValidateUploadFile;
use DigitalCreative\ConditionalContainer\ConditionalContainer;
use DigitalCreative\ConditionalContainer\HasConditionalContainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class PosUser extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;
    public static $displayInNavigation = false;

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Pos users');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Pos user');
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email', 'mobile', 'business_name_en', 'business_name_ar'
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

            Text::make(__('merchant name'), 'name')
                ->sortable()
                ->rules('required', 'max:50'),

            Text::make(__('Business Name'), function(){
                return $this->mainStoreUser ? $this->mainStoreUser->business_name_en : $this->business_name_en;
            })->rules('required', 'max:50'),

            Image::make(__('Business logo'), 'logo')
                ->disk('public')
                ->rules(new ValidateUploadFile(['png', 'jpg', 'jpeg']))
                ->preview(function ($value) {
                    if(Storage::disk('public')->exists($value)){
                        return url('storage/'.$value);
                    }else{
                        if($value){
                            return url($value);
                        }else{
                            return '/images/no-image.jpg';
                        }
                    }
                })
                ->thumbnail(function ($value) {
                    if(Storage::disk('public')->exists($value)){
                        return url('storage/'.$value);
                    }else{
                        if($value){
                            return url($value);
                        }else{
                            return '/images/no-image.jpg';
                        }
                    }
                })->disableDownload()->hideWhenUpdating($this->store_main_user_id ? true : false)->hideFromDetail($this->store_main_user_id ? true : false),

            Text::make(__('Email'), 'email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}')
                ->hideFromIndex(),

            Password::make(__('Password'), 'password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Text::make(__('Mobile'), 'mobile')->rules('required', 'regex:/(^[5]{1}[0-9]{8}$)/')->onlyOnForms(),

            Text::make(__('Mobile'), 'mobile')->displayUsing(function(){
                $yes = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="check-circle" role="presentation" class="fill-current text-success"><path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"></path></svg>';
                $no = ' <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="x-circle" role="presentation" class="fill-current text-danger"><path d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"></path></svg>';

                return ($this->mobile_verified) ? $this->mobile . $yes : $this->mobile . $no ;
            })->asHtml()->onlyOnDetail(),

            Select::make(__('Mobile Verified'), 'mobile_verified')
                ->options([
                    '1' => __('verified'),
                    '0' => __('unverified'),
                ])
                ->displayUsingLabels()
                ->sortable()
                ->onlyOnForms()
                ->hideWhenUpdating($this->store_main_user_id ? true : false)
                ->hideFromDetail($this->store_main_user_id ? true : false),

            Select::make(__('Gender'), 'gender')
                ->options([
                    '0' => '-',
                    '1' => __('Male'),
                    '2' => __('Female'),
                ])
                ->displayUsingLabels()
                ->sortable()
                ->hideFromIndex(),

            new Panel(__('Business Information'), $this->businessInformation()),

        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('source', 'pos');
    }

    /**
     * Get the address fields for the resource.
     *
     * @return array
     */
    protected function businessInformation()
    {
        return [
            Select::make(__('License Type'), 'license_type')->options([
                'Commercial Record' => __('Commercial Record'),
                'Freelance' => __('Freelance'),
            ])->displayUsingLabels()->hideFromIndex(),

            ConditionalContainer::make([Date::make(__('Commercial Registry Expiry Date'), 'commercial_registry_expiry_date')->hideFromIndex()])
                ->if('license_type = Commercial Record')->hideFromIndex(),

            Text::make(__('VAT Registration Number'), 'vat_registration_number')->hideFromIndex(),
            Text::make(__('Business Name').' en', 'business_name_en')->hideFromIndex(),
            Text::make(__('Business Name').' ar', 'business_name_ar')->hideFromIndex(),
            Textarea::make(__('Business Address'), 'business_address')->hideFromIndex(),
            Text::make(__('Mobile'), 'business_mobile')->hideFromIndex(),
        ];
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
