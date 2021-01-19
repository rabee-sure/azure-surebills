@extends('layouts.bill')

@section('title', __('Bill') . ' ' . $bill->number)

@section('content')
  <div class="single_bill_page"  id="app">
    <div class="container">
      <div class="row  justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 col-xl-6">
          <div class="single_bill_content">
            {{--         <a onclick="window.print(); return false;" class="float-right btn btn-primary mr-2 mb-2 rounded-sm d-inline-block " href="#" title="{{ __('Print') }}">
          <img src="{{ asset('img/printer.svg') }}" alt="{{ __('Print') }}" style="height: 25px;">
        </a> --}}
            <div class="change-lang">
            @if($bill->user->settings->active_lang == 'all')
              @if(App::isLocale('en'))
                <a href="{{ $bill->pay_url }}/ar" title="عربي">عربي</a>
              @else
                <a href="{{ $bill->pay_url }}/en" title="English">English</a>
              @endif
            @endif
          </div>
            @if($bill->user->logo)
              <div class="logo">
                <img src="{{ $bill->user->logo_url }}" alt="logo">
              </div><!-- logo -->
            @endif

            @if($bill->application_id == null)
              <div class="title">
                <span>{{ $bill->user->business_name }}</span>

                @if(isset($bill->user->settings->header_bill))
                  <p>{{ $bill->user->settings->header_bill }}</p>
                @endif

                <p>{{  $bill->user->business_address }}</p>
                <b>{{  $bill->user->business_mobile }}</b>
              </div><!-- title -->
            @endif

            <div id="status">
              @if($bill->status == 'expired')
                <div class="alert alert-danger" role="alert">
                  {{ __('this bill has been expired', ['number' => $bill->number ]) }}
                </div>
              @endif
              @if($bill->status == 'paid')
                <div class="alert alert-success" role="alert">
                  @if ($bill->depositTransaction)
                    {{ __('Paid') }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
                  @else
                  {{ __('this bill has been successfully', ['number' => $bill->number ]) }}
                  @endif
                </div>
              @endif
              @if($bill->status == 'canceled')
                <div class="alert alert-danger" role="alert">
                  {{ __('this bill has been canceled', ['number' => $bill->number ]) }}
                </div>
              @endif
            </div>

            @if($bill->application_id == null)
              <div class="date_time">
                <span>
                  {{__('Due on')}} {{ $bill->dateLocalization()}}
                  @if($bill->user->vat_registration_number)
                    <div class="vat_reg"> {{ __('VAT Registration Number') }} : {{ $bill->user->vat_registration_number }}</div>
                  @endif
                </span>
                <div>
                  <p>{{ __('Bill') }} #{{ $bill->number }}</p>
                  <b>{{ $bill->created_at->format('Y/m/d')}}</b>
                </div>
              </div><!-- date_time -->
              <div class="shopping_cart">
                @foreach($bill->items as $item)
                  <div class="details_pay">
                    <p>{!! $item->product_name !!}</p>
                    <b>X {{ $item->quantity  }}</b>
                    <b>{{ $item->product_price  }} {{ __('SAR') }}</b>
                  </div><!-- details_pay -->
                @endforeach
              </div><!-- shopping_cart -->
              <div class="total_bill">
                @if( $bill->add_tax && $bill->add_discount)
                  <p>{{ __('Subtotal') }} : {{ $bill->sub_total }} {{ __('SAR') }}</p>
                @endif
                @if( $bill->add_discount)
                  @if($bill->discount_type == 'percentage')
                    <p>{{ __('Discount') }} ({{ $bill->discount_value }}%) : {{ $bill->discount }} {{ __('SAR') }}</p>
                  @else
                    <p>{{ __('Discount') }}  ({{ $bill->discount_value }} {{ __('SAR') }}) : {{ $bill->discount }} {{ __('SAR') }}</p>
                  @endif
                  <p>{{ __('Subtotal - Discount') }} : {{ $bill->sub_total- $bill->discount }} {{ __('SAR') }}</p>
                @endif
                @if( $bill->add_tax)
                  <p>{{ __('Vat') }} ({{ $bill->tax_value }}%) : {{ $bill->vat }} {{ __('SAR') }}</p>
                @endif
                <b>{{ __('Total') }} : {{ $bill->total}} {{ __('SAR') }}</b>
              </div><!-- total_bill -->
              @if($bill->customer_notes)<div class="customer_notes">{{$bill->customer_notes}}</div> @endif

            @else
              <div class="date_time">
                <b>{{ $bill->total}}  {{ __('SAR') }}</b>
              </div>
            @endif
            
            @if($bill->application && $bill->status != 'paid')
              <div id="back_btn" class="text-center">
                <a href="{{ $bill->back_url}}" class="btn btn-light">{{__('Back')}}
                  <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M7.854 4.646a.5.5 0 0 1 0 .708L5.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0z"/>
                    <path fill-rule="evenodd" d="M4.5 8a.5.5 0 0 1 .5-.5h6.5a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5z"/>
                  </svg>
                </a>
              </div>
            @endif

          </div><!-- single_bill_content -->
          <a href="/" target="_blank" title="Sure Bills" class="logo_bills"></a>
        </div><!-- col-12 -->
      </div><!-- row -->
    </div><!-- container -->


  </div><!-- single_bill_page -->
@endsection


@push('footer-scripts')
<script type="text/javascript">
  /* New countdown */
  $(function(){
    let searchParams = new URLSearchParams(window.location.search)
    if(searchParams.has('print')){
      window.print();
    }
  });
  /* New countdown */

  Echo.channel('bill.{{$bill->id}}')
    .listen('BillStatusUpdated', (e) => {
        console.log(e.bill.id);
        var className;

        switch(e.bill.status) {
          case "pending":
            className = "badge-info";
            break;
          case "paid":
            $("#payment_method").remove();
            $("#back_btn").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
            break;
          case "canceled":
            $("#payment_method").remove();
            $("#back_btn").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-danger" role="alert">{{ __("this bill is canceled") }}</div>');
            break;
          case "expired":
            $("#payment_method").remove();
            $("#back_btn").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-secondary" role="alert">{{ __("this bill is expired") }}</div>');
            break;
          default:
            $("#payment_method").remove();
            $("#back_btn").remove();
            $("#status").empty();
            className = "badge-info";
        }
        // $('#status')
        //   .text(e.bill.trans_status)
        //   .removeClass('badge-light badge-danger badge-success badge-info')
        //   .addClass(className);
    });
</script>
    {!! JsValidator::formRequest('App\Http\Requests\PayBillRequest', '#bill_bay') !!}
@endpush
