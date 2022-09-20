<?php

namespace App\Nova;

use App\Nova\Actions\BillsExcelDownload;
use App\Nova\Filters\BillSettled;
use App\Nova\Filters\BillSource;
use App\Nova\Filters\BillStatus;
use App\Nova\Filters\DateRange;
use App\Nova\Filters\PaidDateRange;
use App\Nova\Filters\RefundedDateRange;
use App\Nova\Filters\UserId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Timothyasp\Badge\Badge;
use Titasgailius\SearchRelations\SearchesRelations;
use PosLifestyle\DateRangeFilter\DateRangeFilter;

class Bill extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Bill::class;

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Bills');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Bill');
    }

    /**
     * Remove Zero Transactions rubbish from database
     * @Todo : Remember to retrieve also here all transactions that don't relate to reservation
     * @param NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // public static function indexQuery(NovaRequest $request, $query)
    // {
    //     $filters = json_decode(base64_decode(\request('filters')), true);

    //     dd( $filters);
    //     collect($filters)->each(function ($filter) use ($request, $query) {
    //         if (!is_null($filter['value']) and !empty($filter['value'])) {
    //             (new $filter['class'])->apply($request, $query, $filter['value']);
    //         }
    //     });

    //     return $query;
    // }


    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'number';

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'customer' => ['name', 'mobile'],
    ];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'number',
        'customer_name',
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
        $options = [
            'pending' => __('Pending'),
            'paid' =>  __('Paid'),
            'canceled' =>  __('Canceled'),
            'expired' =>  __('Expired'),
            'refunded' =>  __('Refunded'),
            'refunded_cash' =>  __('Refunded Cash'),
            'refunded_bank_transfer' =>  __('Refunded Bank Transfer'),
            'paid_cash' =>  __('Paid Cash'),
            'paid_bank_transfer' =>  __('Paid Bank Transfer'),
            'failed' =>  __('Failed'),
        ];
        return [

            Text::make(__('Bill num'), 'number'),

            Text::make(__('Name'), function () {
                return __('Bill') . ' ' .  $this->number  . '-' . $this->customer_name;
            }),

            Badge::make(__('Status'), 'status')
                ->options($options)
                ->colors([
                    'pending' => '#3195a5',
                    'paid' => '#3e884f',
                    'canceled' => '#c43d4b',
                    'expired' => '#ececec',
                    'refunded' => '#b69329',
                    'refunded_bank_transfer' => '#b69329',
                    'refunded_cash' => '#b69329',
                    'paid_cash' => '#3e884f',
                    'paid_bank_transfer' => '#3e884f',
                    'failed' => '#c43d4b',
                ])->displayUsingLabels(),

            Text::make(__('Url'), 'pay_url')
                ->displayUsing(function () {
                    return '<a href="' . $this->pay_url . '" target="_blank" class="no-underline dim text-primary  view_reservation">' . __('Bill link') . '</a>';
                })
                ->sortable()
                ->onlyOnDetail()
                ->asHtml(),

            BelongsTo::make(__('Application'), 'application', Application::class)
                ->onlyOnDetail(),


            Text::make(__('Channel'), 'pay_url')
                ->displayUsing(function () {
                    $id = $this->application->channel->id ?? '';
                    $name = $this->application->channel->name ?? '---';
                    if (!empty($id)) {
                        return '<a href="/nova/resources/channels/' . $id . '" class="no-underline dim text-primary view_reservation">' . $name . '</a>';
                    } else {
                        return '--';
                    }
                })
                ->onlyOnDetail()
                ->asHtml(),

            Select::make(__('Payment Method'), 'payment_method')
                ->options([
                    'credit' => 'credit',
                    'stc' => 'stc',
                    'apple' => 'apple',
                ])->onlyOnDetail(),

            Number::make(__('Refunded Amount'), 'refund_amount')
                ->onlyOnDetail(),

            Number::make(__('Total'), 'total')
                ->min(1)
                ->step(0.1),

            Number::make(__('Payment Fees'), 'payment_fees', function () {
                return (string) $this->payment_fees;
            })->min(1)->step(0.1)->onlyOnDetail(),

            Number::make(__('discount'), 'discount')
                ->min(1)
                ->step(0.1)
                ->onlyOnDetail(),

            Number::make(__('Tax'), 'vat')
                ->min(1)
                ->step(0.1)
                ->onlyOnDetail(),

            Text::make(__('Reference Id'), 'reference_id'),

            $request->user()->can('show merchants') ? BelongsTo::make(__('Merchant'), 'user', User::class) : Text::make(__('Merchant'), function(){return $this->user->name;}),

            // DateTime::make(__('Created At'), 'created_at')->exceptOnForms(),

            Text::make(__('Created At'), 'created_at')
                ->displayUsing(function () {
                    if ($this->created_at)
                        return $this->created_at->format('Y-m-d h:i a');
                    else
                        return 'NULL';
                })->exceptOnForms(),

            BelongsTo::make(__('Customer'), 'customer', Customer::class)->onlyOnDetail(),
            Text::make(__('Business Name'), 'business_name')->onlyOnDetail(),
            Date::make(__('Due Date'), 'due_date')->onlyOnDetail(),
            DateTime::make(__('Paid At'), 'paid_at')->onlyOnDetail(),
            DateTime::make(__('Canceled At'), 'canceled_at')->onlyOnDetail(),
            Boolean::make(__('Send Email'), 'send_email')->onlyOnDetail(),
            Boolean::make(__('Send Sms'), 'send_sms')->onlyOnDetail(),

            new Panel(__('Payment Details'), function () {
                return [
                    Text::make(__('Method Type'), 'payment_method_details')->onlyOnDetail(),

                    Number::make(__('Sub Total'), 'sub_total')->min(1)->step(0.1)->onlyOnDetail(),
                    Number::make(__('Discount'), 'discount')->min(1)->step(0.1)->onlyOnDetail(),
                    Number::make(__('Tax'), 'vat')->min(1)->step(0.1)->onlyOnDetail(),
                    Number::make(__('Total'), 'total')->min(1)->step(0.1)->onlyOnDetail(),
                    Number::make(__('Payment Fees'), function () {
                        if ($this->status == 'refunded')
                            return '0';
                        else
                            return round($this->payment_fees, 2);
                    })->min(1)->step(0.1)->onlyOnDetail(),
                    Number::make(__('Payment Fees VAT'), function () {
                        if ($this->status == 'refunded')
                            return '0';
                        else
                            return  round($this->payment_fees_vat, 2);
                    })->min(1)->step(0.1)->onlyOnDetail(),

                    Number::make(__('Payment Surebills Fees'), function () {
                        if ($this->status == 'refunded')
                            return '0';
                        else
                            return round($this->payment_surebills_fees, 2);
                    })->min(1)->step(0.1)->onlyOnDetail(),
                    Number::make(__('Payment Surebills Fees Vat'), function () {
                        if ($this->status == 'refunded')
                            return '0';
                        else
                            return round($this->payment_surebills_fees_vat, 2);
                    })->min(1)->step(0.1)->onlyOnDetail(),
                    Number::make(__('Payment Channel Fees'), function () {
                        if ($this->status == 'refunded')
                            return '0';
                        else
                            return round($this->payment_channel_fees, 2);
                    })->min(1)->step(0.1)->onlyOnDetail(),
                    Number::make(__('Payment Channel Fees Vat'), function () {
                        if ($this->status == 'refunded')
                            return '0';
                        else
                            return round($this->payment_channel_fees_vat, 2);
                    })->min(1)->step(0.1)->onlyOnDetail(),

                    Number::make(__('Due to client'), function () {
                        if ($this->status == 'refunded')
                            return '0';
                        else
                            return round($this->due_to_client, 2);
                    })->min(1)->step(0.1)->onlyOnDetail(),
                ];
            }),
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
        return [
            new BillStatus,
            new BillSource,
            new DateRangeFilter(__('Date Range'), 'created_at'),
            new DateRangeFilter(__('Paid at Date Range'), 'paid_at'),
            new DateRangeFilter(__('Refunded at Date Range'), 'refunded_at'),
            new UserId(),
            // new BillSettled(),
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
            (new BillsExcelDownload(Auth::user()->email, $request->toArray()))->standalone()->canRun(function (NovaRequest $request) {
                return true;
            }),
            // (new DownloadExcel)->withHeadings(),
        ];
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
     * authorized To Update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    public function authorizedToUpdate(Request $request)
    {
        return false;
    }
}
