<?php

namespace App\Nova;

use App\Nova\Filters\DateRange;
use App\Nova\Filters\UserBalance;
use App\Nova\Filters\UserId;
use App\Nova\Filters\UsersVerified;
use App\Nova\Filters\UsersUnverified;
use App\Nova\Metrics\NewBills;
use App\Rules\ValidateUploadFile;
use Ebess\AdvancedNovaMediaLibrary\Fields\Files;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Sure\Userstats\Userstats;
use PosLifestyle\DateRangeFilter\DateRangeFilter;

class VrificationRequests extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;
    private $verifyStatus = '';

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Verification Requests');
    }

    public static $displayInNavigation = false;

    /**
     * Remove Zero Transactions rubbish from database
     * @Todo : Remember to retrieve also here all transactions that don't relate to reservation
     * @param NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->vrificationRequest()->where('source', '<>', 'pos');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Merchant');
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

            Text::make(__('Balance'), function () {
                return $this->round_balance;
            })->readonly(),

            Text::make(__('Business Name'), 'business_name_en')->rules('required', 'max:50'),
            new Panel(__('Bank Information'), $this->bankInformation()),
            Text::make(__('Account Name'), 'name')
                ->sortable()
                ->rules('required', 'max:50'),

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

            new Panel(__('Pricing'), $this->pricingFields()),

            new Panel(__('Business Information'), $this->businessInformation()),

            Image::make(__('Business logo'), 'logo')
                ->disk('public')
                ->path(merchant_logo_disk_path())
                ->rules(new ValidateUploadFile(['png', 'jpg', 'jpeg']))
                ->preview(fn ($value) => merchant_logo_url($value) ?: '/images/no-image.jpg')
                ->thumbnail(fn ($value) => merchant_logo_url($value) ?: '/images/no-image.jpg')
                ->disableDownload(),

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
            Files::make(__('Business Documents'), 'business_documents')->hideFromIndex()->hideFromDetail()->rules(new ValidateUploadFile(['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xlsx', 'csv'])),

            // Custom version that mimics Nova gallery but removes the eye icon
            Text::make(__('Business Documents'), function () {
                $mediaItems = $this->getMedia('business_documents');

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


            Boolean::make(__('Disable Business Documents'), 'disable_business_documents')->hideFromIndex(),

            Files::make(__('Bank Documents'), 'bank_documents')->hideFromIndex()->hideFromDetail()->rules(new ValidateUploadFile(['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xlsx', 'csv'])),

            // Custom version that mimics Nova gallery but removes the eye icon
            Text::make(__('Bank Documents'), function () {
                $mediaItems = $this->getMedia('bank_documents');

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
            Boolean::make(__('Disable Bank Documents'), 'disable_bank_documents')->hideFromIndex(),

            Boolean::make(__('Verified'), 'verified')
                ->hideFromIndex(),

            Text::make(__('Verified'), 'verified')->displayUsing(function(){
                $yes = '<img src="/images/verified.svg" style="height: 30px;">';
                $no = '<img src="/images/verifiedx.svg" style="height: 30px;">';
                return ($this->verified) ?  $yes : $no ;
            })
            ->asHtml()
            ->sortable()
            ->onlyOnIndex(),
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
                ->rules('required', 'numeric', 'max:1000')
                ->step(0.01)
                ->hideFromIndex(),
            Number::make(__('Mada percentage fees'), 'mada_percentage')
                ->rules('required', 'numeric', 'max:100')
                ->step(0.01)
                ->hideFromIndex(),
            Number::make(__('Credit Card fixed fees'), 'credit_cards_fixed')
                ->rules('required', 'numeric', 'max:1000')
                ->step(0.01)
                ->hideFromIndex(),
            Number::make(__('Credit Card percentage fees'), 'credit_cards_percentage')
                ->rules('required', 'numeric', 'max:100')
                ->step(0.01)
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
                'Commercial Record' => __('Commercial Record'),
                'Freelance' => __('Freelance'),
            ])->displayUsingLabels()->hideFromIndex(),
            Text::make(__('VAT Registration Number'), 'vat_registration_number')->hideFromIndex(),
            Text::make(__('Business Name').' en', 'business_name_en')->hideFromIndex(),
            Text::make(__('Business Name').' ar', 'business_name_ar')->hideFromIndex(),
            Text::make(__('Sector'), 'sector')->hideFromIndex(),
            Text::make(__('City'), 'business_address')->hideFromIndex(),
            Text::make(__('Mobile'), 'business_mobile')->hideFromIndex(),
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
            BelongsTo::make(__('Bank'), 'bank', Bank::class)->hideFromIndex(),
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
            new DateRangeFilter(__('Date Range'), 'created_at'),
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
            (new DownloadExcel)
                ->only(['id', 'balance_string', 'Business_name', 'bank_name', 'iban_number', 'name', 'verify_status'])
                ->withHeadings(['ID', __('Balance'), __('Business Name'), __('Bank'), __('Iban Number'), __('Account Name'), __('Verified')]),
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

    /**
     * authorized To Create.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Laravel\Nova\Fields\Field  $field
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableBanks(NovaRequest $request, $query)
    {
        return $query->where('active', true);
    }
}
