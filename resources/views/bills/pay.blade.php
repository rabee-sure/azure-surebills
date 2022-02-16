@extends('layouts.bill')
@section('title', __('Bill No.') . ' ' . $bill->number)
@section('content')

  <div class="loading"></div>

  <div class="singlebBillSimple_page d-flex align-items-center justify-content-center flex-column">
    <div class="all_bill_page">
      <div class="change_lang d-flex align-items-center justify-content-end w-100 mb-1">
        @if($bill->user->settings->active_lang == 'all')
          @if(App::isLocale('en'))
            <a href="{{ route('paybillpagelang', ['id' => $bill->pay_id, 'lang' => 'ar'])}}" title="عربي" class="d-block">عربي</a>
          @else
            <a href="{{ route('paybillpagelang', ['id' => $bill->pay_id, 'lang' => 'en'])}}" title="English" class="d-block">English</a>
          @endif
        @endif
      </div><!-- change_lang -->
      <div class="single_bill_content">
        <div class="about d-flex align-items-center justify-content-center flex-column">
          @if($bill->user->logo)
            <img src="{{ $bill->user->logo_url }}" alt="logo">
          @endif
          <span class="d-block font-weight-bold">{{ $bill->user->business_name }}</span>
          @if(isset($bill->user->settings->header_bill))
            <p class="d-block mb-0">{{ $bill->user->settings->header_bill }}</p>
          @endif
          <p class="d-block mb-0">{{  $bill->user->business_address }}</p>
          <b class="d-block font-weight-normal">{{  $bill->user->business_mobile }}</b>
        </div><!-- about -->
        @if($bill->application_id && !$bill->is_expired && $bill->remaining_time_hours['hours'] == '00' && $bill->remaining_time_hours['days'] == 0)
          <div class="countdown alert alert-warning d-flex align-items-center justify-content-center" id="new_countdown">
            <p class="mb-0">{{ __('the bill will expire in')}}</p>
            <span id="hm_timer"></span>
          </div><!-- countdown -->
        @endif
        @if($bill->status == 'expired')
          <div id="status">
            <div class="alert alert-danger"> {{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->
        @elseif($bill->status == 'paid')
          <div id="status">
            <div class="alert alert-success"> 
              @if ($bill->depositTransaction)
                {{ __('Paid') }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
              @else
                {{ __('this bill has been successfully', ['number' => $bill->number ]) }}
              @endif
            </div>
          </div><!-- status -->
        @elseif($bill->status == 'paid_cash')
          <div id="status">
            <div class="alert alert-success"> {{ __('this bill has been Paid Cash successfully', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->
        @elseif($bill->status == 'paid_bank_transfer')
          <div id="status">
            <div class="alert alert-success"> {{ __('this bill has been Paid Bank Transfer successfully', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->
        @elseif($bill->status == 'canceled')
          <div id="status">
            <div class="alert alert-danger"> {{ __('this bill has been canceled', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->         
        @elseif($bill->status == 'failed')
          <div id="status">
            <div class="alert alert-danger"> {{ __('this bill has been failed', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->     
        @elseif(in_array($bill->status, ['refunded', 'refunded_cash', 'refunded_bank_transfer']))
          <div id="status">
            <div class="alert alert-warning"> {{ __('this bill has been refunded', ['number' => $bill->number ]) }}</div>
          </div><!-- status -->
        @endif
        @if($errors->any())
          <div class="anyErrors alert alert-danger" role="alert">{{ __($errors->first()) }}</div>
        @endif
        <div class="bill_info">
          <div class="d-flex align-items-center justify-content-between">
            <span>{{__('Due on')}}</span>
            <span>{{ $bill->dateLocalization()}}</span>
          </div><!-- d-flex -->
          @if($bill->user->vat_registration_number)
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('VAT Registration Number') }}</span>
              <span>{{ $bill->user->vat_registration_number }}</span>
            </div><!-- d-flex -->
          @endif
          <div class="d-flex align-items-center justify-content-between">
            <span>{{ __('Bill No.') }}</span>
            <span>#{{ $bill->number }}</span>
          </div><!-- d-flex -->
          <div class="d-flex align-items-center justify-content-between">
            <span>{{ $bill->created_at->format('Y/m/d')}}</span>
          </div><!-- d-flex -->
        </div><!-- bill_info -->
        <div class="table_items">
          <table>
            <thead>
              <tr>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Quantity') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($bill->items as $item)
              <tr>
                <td>{!! $item->product_name !!}</td>
                <td>{{ $item->product_price  }} {{ __('SAR') }}</td>
                <td>{{ $item->quantity  }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div><!-- table_items -->
        <div class="bill_info">
          @if( $bill->add_tax || $bill->add_discount)
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Subtotal') }}</span>
              <span>{{ $bill->sub_total }} {{ __('SAR') }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->add_discount)
            @if($bill->discount_type == 'percentage')
              <div class="d-flex align-items-center justify-content-between">
                <span>{{ __('Discount') }} ({{ $bill->discount_value }}%)</span>
                <span>{{ $bill->discount }}  {{ __('SAR') }}</span>
              </div><!-- d-flex -->
            @else
              <div class="d-flex align-items-center justify-content-between">
                <span>{{ __('Discount') }} ({{ $bill->discount_value }}  {{ __('SAR') }})</span>
                <span>{{ $bill->discount }}  {{ __('SAR') }}</span>
              </div><!-- d-flex -->
            @endif
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Subtotal - Discount') }}</span>
              <span>{{ $bill->sub_total- $bill->discount }}  {{ __('SAR') }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->user->pay_fees == 'client')
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('payment fees') }}</span>
              <span>{{ $bill->payment_fees }}  {{ __('SAR') }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->add_tax)
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Vat') }} ({{ $bill->tax_value }}%)</span>
              <span>{{ $bill->vat }}  {{ __('SAR') }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->channel_extra_amount)
            <div class="d-flex align-items-center justify-content-between">
              <span>{{$bill->channel_extra_title}}</span>
              <span>{{ $bill->channel_extra_amount }} {{ __('SAR') }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->channel_extra_vat)
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Vat') }} ({{$bill->channel_extra_title}} ({{ $bill->tax_value }}%))</span>
              <span>{{ $bill->channel_extra_vat }} {{ __('SAR') }}</span>
            </div><!-- d-flex -->
          @endif
          <div class="d-flex align-items-center justify-content-between">
            <span>{{ __('Total') }}</span>
            <span>{{ $bill->total}}  {{ __('SAR') }}</span>
          </div><!-- d-flex -->
        </div><!-- bill_info -->
        @if($bill->customer_notes)
          <div class="customer_notes">{{$bill->customer_notes}}</div> 
        @endif
        @if(!$bill->is_expired)
          <div class="payment_area">@include('bills.payment_page')</div>
        @endif
        @if($bill->status == 'paid' && $bill->user->settings->add_tax_invoice)
          <div class="qrCode_area">
            <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">
              {!! generateQRcode($bill) !!}
              <span>{{ __('Tax Invoice') }}</span>
            </a>
          </div><!-- qrCode_area -->
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
    </div><!-- all_bill_page -->
  </div><!-- singlebBillSimple_page -->
  
@endsection

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
