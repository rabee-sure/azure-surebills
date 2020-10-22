<?php

namespace App\Nova;

use App\Nova\Filters\UserBalance;
use App\Nova\Filters\UserId;
use App\Nova\Metrics\NewBills;
use Ebess\AdvancedNovaMediaLibrary\Fields\Files;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Panel;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Naif\Toggle\Toggle;
use Sure\Userstats\Userstats;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\User::class;

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Users');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('User');
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
        'id', 'name', 'email', 'mobile'
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

            Text::make(__('Balance'), function () {
                return $this->round_balance;
            })->readonly(),

            Text::make(__('Business Name'), 'business_name')->rules('required', 'max:255'),

            Text::make(__('Name'), 'name')
                ->sortable()
                ->rules('required', 'max:255'),            

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
                ->onlyOnForms(),

            Select::make(__('Gender'), 'gender')
                ->options([
                    '0' => '-',
                    '1' => __('Male'),
                    '2' => __('Female'),
                ])
                ->displayUsingLabels()
                ->sortable()
                ->hideFromIndex(),

            new Panel(__('Pricing'), $this->pricingFields()),

            new Panel(__('Business Information'), $this->businessInformation()),
            new Panel(__('Bank Information'), $this->bankInformation()),
            File::make(__('Business logo'), 'logo')->disk('public'),
            HasMany::make(__('Transfers'), 'transfers', Transfer::class),
            // HasMany::make('statement'),
            new Panel(__('Documents'), $this->documents()),

        ];
    }

    /**
     * Get the address fields for the resource.
     *
     * @return array
     */
    protected function documents()
    {
        return [
            Files::make(__('Business Documents'), 'business_documents')->hideFromIndex(),
            Boolean::make(__('Disable Business Documents'), 'disable_business_documents')->hideFromIndex(), 

            Files::make(__('Bank Documents'), 'bank_documents')->hideFromIndex(),
            Boolean::make(__('Disable Bank Documents'), 'disable_bank_documents')->hideFromIndex(),

            Boolean::make(__('Verified'), 'verified'), 
        ];
    }

    /**
     * Get the address fields for the resource.
     *
     * @return array
     */
    protected function pricingFields()
    {
        return [
            Number::make(__('Mada fixed fees'), 'mada_fixed')
                ->rules('required')
                ->step(0.1)
                ->hideFromIndex(),
            Number::make(__('Mada percentage fees'), 'mada_percentage')
                ->rules('required')
                ->step(0.1)
                ->hideFromIndex(),
            Number::make(__('Credit Card fixed fees'), 'credit_cards_fixed')
                ->rules('required')
                ->step(0.1)
                ->hideFromIndex(),
            Number::make(__('Credit Card percentage fees'), 'credit_cards_percentage')
                ->rules('required')
                ->step(0.1)
                ->hideFromIndex(),
        ];
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
                'Commercial Record' => 'Commercial Record',
                'Freelance' => 'Freelance',
            ])->displayUsingLabels()->hideFromIndex(),
            Text::make(__('VAT Registration Number'), 'vat_registration_number')->hideFromIndex(),
            Text::make(__('Business Name'), 'business_name')->hideFromIndex(),
            Text::make(__('Sector'), 'sector')->hideFromIndex(),
            Textarea::make(__('Business Address'), 'business_address')->hideFromIndex(),
            Text::make(__('Mobile'), 'mobile')->hideFromIndex(),
            Text::make(__('Website'), 'website')->hideFromIndex(),
        ];
    }    

    /**
     * Get the address fields for the resource.
     *
     * @return array
     */
    protected function bankInformation()
    {
        return [
            BelongsTo::make(__('Bank'), 'bank', Bank::class),
            Text::make(__('Iban Number'), 'iban_number'),
            Text::make(__('Beneficiary Name'), 'beneficiary_name')->hideFromIndex(),
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
        return [
            (new Userstats)->onlyOnDetail()->width('full'),

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
            new UserBalance,
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
        return [
            new DownloadExcel,
        ];
    }
    
    /**
     * authorized To Delete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    public function authorizedToDelete(Request $request)
    {
        return false;
    }

}
