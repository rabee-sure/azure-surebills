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
                '1' => 'Male',
                '2' => 'Female',
            ])->displayUsingLabels()->sortable(),

            Text::make(__('Balance'), function () {
                return $this->round_balance;
            })->readonly(),
            new Panel(__('Pricing'), $this->pricingFields()),

            new Panel(__('Business Information'), $this->businessInformation()),
            new Panel(__('Bank Information'), $this->bankInformation()),

            HasMany::make(__('Transfers'), 'transfers', Transfer::class),
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
            Number::make(_('mada fixed fees'), 'mada_fixed')->step(0.1)->hideFromIndex(),
            Number::make(_('mada percentage fees'), 'mada_percentage')->step(0.1)->hideFromIndex(),
            Number::make(_('Credit Card fixed fees'), 'credit_cards_fixed')->step(0.1)->hideFromIndex(),
            Number::make(_('Credit Card percentage fees'), 'credit_cards_percentage')->step(0.1)->hideFromIndex(),
            Number::make(_('ApplePay fixed fees'), 'apple_pay_fixed')->step(0.1)->hideFromIndex(),
            Number::make(_('ApplePay percentage fees'), 'apple_pay_percentage')->step(0.1)->hideFromIndex(),
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
            Select::make('License type')->options([
                'Commercial Record' => 'Commercial Record',
                'Freelance' => 'Freelance',
            ])->displayUsingLabels()->onlyOnDetail(),
            Text::make('VAT Registration Number')->onlyOnDetail(),
            Text::make('Business Name')->onlyOnDetail(),
            Text::make('Sector')->onlyOnDetail(),
            Textarea::make('business_address')->onlyOnDetail(),
            Text::make('Mobile')->onlyOnDetail(),
            Text::make('Website')->onlyOnDetail(),
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
            BelongsTo::make('Bank')->onlyOnDetail(),
            Text::make('Iban Number')->onlyOnDetail(),
            Text::make('Beneficiary Name')->onlyOnDetail(),
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
