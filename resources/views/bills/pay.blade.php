@extends('layouts.bill')

@section('title', 'Page Title')

@section('content')


<div class="single_bill_page">
  <div class="container" >

    <div class="row  justify-content-center">
      <div class="col-12 col-md-8 col-lg-6 col-xl-6">
        <div class="single_bill_content">
          <div class="change-lang">
            @if(App::isLocale('en'))
              <a href="{{ $bill->pay_url }}/ar" title="عربي">عربي</a>
            @else
              <a href="{{ $bill->pay_url }}/en" title="English">English</a>
            @endif
          </div>
          @if($bill->user->logo)
            <div class="logo">
              <img src="{{ url($bill->user->logo) }}" alt="logo">
            </div><!-- logo -->
          @endif
          <div class="title">
            <span>{{ $bill->business_name }}</span>
            <p>{{  $bill->user->business_address}}</p>
            <b>{{  $bill->user->business_mobile }}</b>
          </div><!-- title -->


          @if($bill->application_id)
            <div id="countdown" class="border-bottom">
              <div> {{ __('the bill will expire in')}}</div>
            </div>

          @endif
          <div id="status">
          </div>
          @if($errors->any())
            <div class="alert alert-danger" role="alert">
              {{$errors->first()}}
            </div>
          @endif
            <div class="date_time">
              <span>
                {{__('Due on')}} {{ $bill->due_date->format('M d Y')}}
                @if($bill->user->vat_registration_number)
                  <div class="vat_reg"> {{ __('VAT Registration Number') }}: {{ $bill->user->vat_registration_number }}</div>
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
                  <b>{{ $item->product_price  }}  {{ __('SAR') }}</b>
                </div><!-- details_pay -->
              @endforeach
            </div><!-- shopping_cart -->
            <div class="total_bill">
              @if( $bill->add_tax && $bill->add_discount)
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
              @if( $bill->add_tax)
                <p>{{ __('Vat') }} ({{ $bill->tax_value }}%) : {{ $bill->vat }}  {{ __('SAR') }}</p>
              @endif
              <b>{{ __('Total') }} : {{ $bill->total}}  {{ __('SAR') }}</b>
            </div><!-- total_bill -->
            @if($bill->customer_notes)<div class="customer_notes">{{$bill->customer_notes}}</div> @endif
            <div class="customer_information">
              <!-- <div class="name">Customer Information</div> -->
              <p>{{ __('Billed to,') }} {{ $bill->customer_name}}</p>
              <p>+966{{ $bill->customer_mobile}}</p>
              <p>{{ $bill->customer_email}}</p>
            </div><!-- customer_information -->
            <div id="payment_method" class="payment_method">
              <div class="name">{{__('Payment Method')}}</div>
              <div class="bill_payment">
                <div class="item">
                    <input type="radio" id="visa_pay" name="payment_method" value="hyperpay_iframe">
                    <label for="visa_pay">
                    <p>{{ __('Credit Card - mada') }}</p>
                    <div class="icon_mada"></div>
                    <div class="checkmark"></div>
                    </label>
                </div><!-- item -->
                {{-- <div class="item applepay-item">
                    <input type="radio" id="apple_pay" name="payment_method" value="hyperpay_applepay">
                    <label for="apple_pay">
                    <p>{{ __('Apple Pay') }}</p>
                    <div class="icon_apple"></div>
                    <div class="checkmark"></div>
                    </label>
                </div> --}}<!-- item -->
                <div class="item disable">
                    <input type="radio" id="stc_pay" name="payment_method" value="stc_pay" >
                    <label for="pay_3">
                        <p>STC Pay</p>
                        <div class="icon_stc"></div>
                        <div class="checkmark"></div>
                    </label>
                </div><!-- item -->
              </div><!-- bill_payment -->
            </div><!-- payment_method -->
            
            @if($bill->application)
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
          <a href="https://bills.surepay.sa" target="_blank" title="Sure Bills" class="logo_bills"></a>
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
<script type='text/javascript'>
  var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));
  if (isSafari) {
    $('.applepay-item').css('display', 'block');
  }
  jQuery(document).ready(function(){
      $('input:radio[name="payment_method"]').change(function(){
          if (this.checked) {
            $('.visa_pay_content').each(function() {
              $( this ).remove();
            });
            $(this).parent().append('<div class="visa_pay_content" id="iframe_pay">{{ __('Operation is processing...') }}</div>')
            var method = this.value;
            $.ajax({
                type: 'GET', //THIS NEEDS TO BE GET
                url: '/bills/payment_iframe/{{$bill->id}}/' + method+'/{{app()->getLocale()}}',
                success: function (data) {
                     $("#iframe_pay").html(data);
                },
                error: function() { 
                     console.log(data);
                }
            });
          }
      });
  });

$('#countdown').countdown({
    format: 'mm:ss',
    startTime: "{{ $bill->remaining_time}}",
    timerEnd: function() { 
        $("#countdown").remove();
        $("#payment_method").remove();
        $("#back_btn").remove();
        $("#status").empty();
        $("#status").append('<div class="alert alert-secondary" role="alert">this bill has been expired</div>');
  },
    image: "/images/digits.png"
  });
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
            $("#status").append('<div class="alert alert-success" role="alert">this bill paid successfully</div>');
            break;
          case "canceled":
            $("#payment_method").remove();
            $("#back_btn").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-danger" role="alert">this bill has been canceled</div>');
            break;          
          case "expired":
            $("#payment_method").remove();
            $("#back_btn").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-secondary" role="alert">this bill has been expired</div>');
            break;
          default:
            $("#payment_method").remove();
            $("#back_btn").remove();
            $("#status").empty();
            className = "badge-info";
        }
    });
</script>
    {!! JsValidator::formRequest('App\Http\Requests\PayBillRequest', '#bill_bay') !!}
@endpush
