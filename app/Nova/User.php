<?php

namespace App\Nova;

use App\Nova\Filters\UserBalance;
use App\Nova\Filters\UserId;
use App\Nova\Metrics\NewBills;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Panel;
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

            Text::make(__('Business Name'), 'business_name')->rules('required', 'max:255')->onlyOnForms(),

            Text::make(__('Name'), 'name')
                ->sortable()
                ->rules('required', 'max:255'),            

            Text::make(__('Email'), 'email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make(__('Password'), 'password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Text::make(__('Mobile'), 'mobile')->rules('required', 'regex:/(^[5]{1}[0-9]{8}$)/')->onlyOnForms(),

            Select::make(__('Gender'), 'gender')->options([
                '1' => __('Male'),
                '2' => __('Female'),
            ])->displayUsingLabels()->sortable(),

            Text::make(__('Balance'), function () {
                return $this->round_balance;
            })->readonly(),
            new Panel(__('Pricing'), $this->pricingFields()),

            new Panel(__('Business Information'), $this->businessInformation()),
            new Panel(__('Bank Information'), $this->bankInformation()),

            HasMany::make(__('Transfers'), 'transfers', TransferHidden::class),
            // HasMany::make('statement'),

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
            Number::make(__('Mada fixed fees'), 'mada_fixed')->step(0.1)->hideFromIndex(),
            Number::make(__('Mada percentage fees'), 'mada_percentage')->step(0.1)->hideFromIndex(),
            Number::make(__('Credit Card fixed fees'), 'credit_cards_fixed')->step(0.1)->hideFromIndex(),
            Number::make(__('Credit Card percentage fees'), 'credit_cards_percentage')->step(0.1)->hideFromIndex(),
            Number::make(__('ApplePay fixed fees'), 'apple_pay_fixed')->step(0.1)->hideFromIndex(),
            Number::make(__('ApplePay percentage fees'), 'apple_pay_percentage')->step(0.1)->hideFromIndex(),
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
            ])->displayUsingLabels()->onlyOnDetail(),
            Text::make(__('VAT Registration Number'), 'vat_registration_number')->onlyOnDetail(),
            Text::make(__('Business Name'), 'business_name')->onlyOnDetail(),
            Text::make(__('Sector'), 'sector')->onlyOnDetail(),
            Textarea::make(__('Business Address'), 'business_address')->onlyOnDetail(),
            Text::make(__('Mobile'), 'mobile')->onlyOnDetail(),
            Text::make(__('Website'), 'website')->onlyOnDetail(),
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
            BelongsTo::make(__('Bank'), 'bank', Bank::class)->onlyOnDetail(),
            Text::make(__('Iban Number'), 'iban_number')->onlyOnDetail(),
            Text::make(__('Beneficiary Name'), 'beneficiary_name')->onlyOnDetail(),
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
        return [];
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
