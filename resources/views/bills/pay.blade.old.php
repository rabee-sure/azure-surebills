@extends('layouts.bill')

@section('title', __('Bill No.') . ' ' . $bill->number)

@section('content')

<div class="loading"></div>

<div class="single_bill_page">
  <div class="container" >
    <div class="row  justify-content-center">
      <div class="col-12 col-md-8 col-lg-6 col-xl-6">
        <div class="single_bill_content">
        {{-- <a onclick="window.print(); return false;" class="float-right btn btn-primary mr-2 mb-2 rounded-sm d-inline-block " href="#" title="{{ __('Print') }}">
          <img src="{{ asset('images/printer.svg') }}" alt="{{ __('Print') }}" style="height: 25px;">
        </a> --}}
          <div class="change-lang">
            @if($bill->user->settings->active_lang == 'all')
              @if(App::isLocale('en'))
                <a href="{{ route('paybillpagelang', ['id' => $bill->pay_id, 'lang' => 'ar'])}}" title="عربي">عربي</a>
              @else
                <a href="{{ route('paybillpagelang', ['id' => $bill->pay_id, 'lang' => 'en'])}}" title="English">English</a>
              @endif
            @endif
          </div>

          @if($bill->user->logo)
            <div class="logo">
              <img src="{{ $bill->user->logo_url }}" alt="logo">
            </div><!-- logo -->
          @endif

          <div class="title">
            <span>{{ $bill->user->business_name }}</span>

            @if(isset($bill->user->settings->header_bill))
              <p>{{ $bill->user->settings->header_bill }}</p>
            @endif

            <p>{{  $bill->user->business_address}}</p>
            <b>{{  $bill->user->business_mobile }}</b>
          </div><!-- title -->

          @if($bill->application_id && !$bill->is_expired && $bill->remaining_time_hours['hours'] == '00' && $bill->remaining_time_hours['days'] == 0)
            <div class="countdown" id="new_countdown">
                <p>{{ __('the bill will expire in')}}</p>
                <span id="hm_timer"></span>
            </div><!-- countdown -->
          @endif

          <div id="status">
          </div>

          @if($errors->any())
            <div class="alert alert-danger" role="alert">
              {{ __($errors->first()) }}
            </div>
          @endif

            <div class="date_time">
              <span>
                {{__('Due on')}} {{ $bill->dateLocalization()}}
                @if($bill->user->vat_registration_number)
                  <div class="vat_reg"> {{ __('VAT Registration Number') }}: {{ $bill->user->vat_registration_number }}</div>
                @endif
              </span>
              <div>
                <p>{{ __('Bill No.') }} #{{ $bill->number }}</p>
                <b>{{ $bill->created_at->format('Y/m/d')}}</b>
              </div>
            </div><!-- date_time -->
            <div class="shopping_cart">
              @foreach($bill->items as $item)
                <div class="details_pay">
                  <p>{!! $item->product_name !!}</p>
                  <b>X {{ $item->quantity  }}</b>
                  <b>{{ $item->product_price  }}  {{ __('SAR') }}</b>
                </div><!-- details_pay -->
              @endforeach
            </div><!-- shopping_cart -->
            <div class="total_bill">
              @if( $bill->add_tax || $bill->add_discount)
                <p>{{ __('Subtotal') }} : {{ $bill->sub_total }} {{ __('SAR') }}</p>
              @endif
              @if( $bill->add_discount)
                @if($bill->discount_type == 'percentage')
                  <p>{{ __('Discount') }} ({{ $bill->discount_value }}%) : {{ $bill->discount }}  {{ __('SAR') }}</p>
                @else
                  <p>{{ __('Discount') }} ({{ $bill->discount_value }}  {{ __('SAR') }}) : {{ $bill->discount }}  {{ __('SAR') }}</p>
                @endif
                <p>{{ __('Subtotal - Discount') }}: {{ $bill->sub_total- $bill->discount }}  {{ __('SAR') }}</p>
              @endif

              @if( $bill->user->pay_fees == 'client')
                <p>{{ __('payment fees') }} : {{ $bill->payment_fees }}  {{ __('SAR') }}</p>
              @endif
              @if( $bill->add_tax)
                <p>{{ __('Vat') }} ({{ $bill->tax_value }}%) : {{ $bill->vat }}  {{ __('SAR') }}</p>
              @endif

              @if( $bill->channel_extra_amount)
                <p> {{$bill->channel_extra_title}}  : {{ $bill->channel_extra_amount }} {{ __('SAR') }}</p>
              @endif

              @if( $bill->channel_extra_vat)
                <p> {{ __('Vat') }} ({{$bill->channel_extra_title}} ({{ $bill->tax_value }}%))  : {{ $bill->channel_extra_vat }} {{ __('SAR') }}</p>
              @endif

              <b>{{ __('Total') }} : {{ $bill->total}}  {{ __('SAR') }}</b>
            </div><!-- total_bill -->

            @if($bill->customer_notes)
              <div class="customer_notes">{{$bill->customer_notes}}</div>
            @endif
            <div class="customer_information">
              <!-- <div class="name">Customer Information</div> -->
              <p>{{ $bill->customer_name}}</p>
              <p class="ltr">+966{{ $bill->customer_mobile}}</p>
              <p>{{ $bill->customer_email}}</p>
              @if(isset($bill->user->settings->footer_bill))
                <p>{{ $bill->user->settings->footer_bill }}</p>
              @endif
            </div><!-- customer_information -->

            @if(!$bill->is_expired)
              @include('bills.payment_page')
            @endif

            @if($bill->application && $bill->is_redirect)
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
        </div><!-- col-12 -->
    </div><!-- row -->
  </div><!-- container -->
</div><!-- single_bill_page -->

@endsection


@push('styles')
<style type="text/css">
#countdown{
  text-align: center;
  padding-bottom: 100px;
  padding-right: 100px;
  padding-left: 100px;
}
</style>
@endpush

@push('footer-scripts')
<script src="{{ asset('js/jquery.countdownTimer.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-migrate-1.2.1.js"></script>

<script type='text/javascript'>
/* New countdown */
$(function(){
  $("#hm_timer").countdowntimer({
    minutes : {{ $bill->remaining_time_minutes}},
    seconds : {{ $bill->remaining_time_seconds}},
    size : "lg",
    timeUp : timeisUp
  });

  function timeisUp() {
    $("#new_countdown").remove();
    $("#payment_area").remove();
    // $("#back_btn").remove();
    $("#status").empty();
    $("#status").append('<div class="alert alert-danger" role="alert">{{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
  }
});
/* New countdown */

  Echo.channel('bill.{{$bill->id}}')
    .listen('BillStatusUpdated', (e) => {

        var className;

        switch(e.bill.status) {
          case "pending":
            className = "badge-info";
            break;
          case "paid":
            $("#new_countdown").remove();
            $("#payment_area").remove();
            $("#back_btn").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
            break;
          case "canceled":
            $("#new_countdown").remove();
            $("#payment_area").remove();
            $("#back_btn").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-danger" role="alert">{{ __("this bill is canceled") }}</div>');
            break;
          case "expired":
            $("#new_countdown").remove();
            $("#payment_area").remove();
            $("#back_btn").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-danger" role="alert">{{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
            break;
          default:
            $("#payment_area").remove();
            $("#back_btn").remove();
            $("#status").empty();
            className = "badge-info";
        }
    });
</script>


@endpush
