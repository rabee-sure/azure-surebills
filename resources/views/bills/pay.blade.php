@extends('layouts.bill')

@section('title', __('Bill') . ' ' . $bill->number)

@section('content')



<div class="loading"></div>


<div class="single_bill_page">
  <div class="container" >
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
              <img src="{{ url($bill->user->logo) }}" alt="logo">
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

          @if($bill->application_id && !$bill->is_expired)
            <div class="countdown" id="new_countdown">
              <p>{{ __('the bill will expire in')}}</p>
              @if($bill->remaining_time_hours['hours'] == '00' && $bill->remaining_time_hours['days'] == 0)
                <span id="hm_timer" ></span>
              @else
                @if($bill->remaining_time_hours['days'] > 0)
                  <p>{{$bill->remaining_time_hours['days']}} {{__('Day') }} </p>
                @endif
                @if($bill->remaining_time_hours['hours'] > 0)
                  <span> {{$bill->remaining_time_hours['hours']}} {{__('Hour')}}</span>
                @endif
              @endif
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
              @if( $bill->user->pay_fees == 'client')
                <p>{{ __('payment fees') }} : {{ $bill->payment_fees }}  {{ __('SAR') }}</p>
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
              <p class="ltr">+966{{ $bill->customer_mobile}}</p>
              <p>{{ $bill->customer_email}}</p>

              @if(isset($bill->user->settings->footer_bill))
                <p>{{ $bill->user->settings->footer_bill }}</p>
              @endif

            </div><!-- customer_information -->
            @if(!$bill->is_expired)
                <div id="payment_method" class="payment_method">
              <div class="name">{{__('Payment Method')}}</div>
              <div class="bill_payment">

                {{-- mastercard --}}
                {{-- <div class="item">
                    <input type="radio" id="mastercard_pay" name="payment_method" value="mastercard_iframe">
                    <label for="mastercard_pay">
                    <p>{{ __('Credit Card - mada') }}</p>
                    <div class="icon_mada"></div>
                    <div class="checkmark"></div>
                    </label>
                </div> --}}
                {{-- mastercard --}}

                {{-- Apple pay mastercard --}}
                <div id="applepay_show" class="item">
                    <input type="radio" id="mastercard_applepay" name="payment_method" value="mastercard_applepay">
                    <label for="mastercard_applepay">
                    <p>{{ __('Apple Pay') }}</p>
                    <div class="icon_apple"></div>
                    <div class="checkmark"></div>
                    </label>
                    <div style="display: none;" class="apple_pay_content">
                      <button id="payment" lang="<?php echo App::getLocale() ?>" style="-webkit-appearance: -apple-pay-button; -apple-pay-button-type: buy; cursor: pointer; border-radius: 5px;"></button>
                    </div>
                </div><!-- item -->
                {{-- mastercard --}}

              </div><!-- bill_payment -->
                </div><!-- payment_method -->
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
/* Absolute Center Spinner */
.loading {
  position: fixed;
  z-index: 999;
  height: 2em;
  width: 2em;
  overflow: show;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  display: none;
}

/* Transparent Overlay */
.loading:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #000;
  opacity: 0.6;
}

/* :not(:required) hides these rules from IE9 and below */
.loading:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

.loading:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 1em;
  height: 1em;
  margin-top: -0.5em;
  -webkit-animation: spinner 150ms infinite linear;
  -moz-animation: spinner 150ms infinite linear;
  -ms-animation: spinner 150ms infinite linear;
  -o-animation: spinner 150ms infinite linear;
  animation: spinner 150ms infinite linear;
  border-radius: 0.5em;
  -webkit-box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;
box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;
}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
</style>

@endpush

@push('footer-scripts')
  <script src="{{ asset('js/jquery.countdownTimer.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-migrate-1.2.1.js"></script>

<script src="{{config('payment.drivers.mastercard_iframe.session_script')}}"></script>
<script src="{{config('payment.drivers.mastercard_iframe.checkout_script')}}"
        data-cancel="cancelCallback"
        data-timeout="cancelCallback"
        data-complete="completeCallback"></script>

<script type='text/javascript'>
    function cancelCallback()
    {
        $('.loading').hide();
        $('#mastercard_pay').prop('checked', false);
        $('.visa_pay_content').remove();
    }
    function completeCallback(resultIndicator, sessionVersion)
    {
        $('.loading').hide();
        $(".mastercardPaymentWidgets" ).submit();
    }

var BrowserDetect = {
init: function () {
    this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
    this.version = this.searchVersion(navigator.userAgent)
        || this.searchVersion(navigator.appVersion)
        || "an unknown version";
    this.OS = this.searchString(this.dataOS) || "an unknown OS";
},
searchString: function (data) {
    for (var i=0;i<data.length;i++)    {
        var dataString = data[i].string;
        var dataProp = data[i].prop;
        this.versionSearchString = data[i].versionSearch || data[i].identity;
        if (dataString) {
            if (dataString.indexOf(data[i].subString) != -1)
                return data[i].identity;
        }
        else if (dataProp)
            return data[i].identity;
    }
},
searchVersion: function (dataString) {
    var index = dataString.indexOf(this.versionSearchString);
    if (index == -1) return;
    return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
},
dataBrowser: [
    {
        string: navigator.userAgent,
        subString: "Chrome",
        identity: "Chrome"
    },
    {     string: navigator.userAgent,
        subString: "OmniWeb",
        versionSearch: "OmniWeb/",
        identity: "OmniWeb"
    },
    {
        string: navigator.vendor,
        subString: "Apple",
        identity: "Safari",
        versionSearch: "Version"
    },
    {
        prop: window.opera,
        identity: "Opera",
        versionSearch: "Version"
    },
    {
        string: navigator.vendor,
        subString: "iCab",
        identity: "iCab"
    },
    {
        string: navigator.vendor,
        subString: "KDE",
        identity: "Konqueror"
    },
    {
        string: navigator.userAgent,
        subString: "Firefox",
        identity: "Firefox"
    },
    {
        string: navigator.vendor,
        subString: "Camino",
        identity: "Camino"
    },
    {        // for newer Netscapes (6+)
        string: navigator.userAgent,
        subString: "Netscape",
        identity: "Netscape"
    },
    {
        string: navigator.userAgent,
        subString: "MSIE",
        identity: "Explorer",
        versionSearch: "MSIE"
    },
    {
        string: navigator.userAgent,
        subString: "Gecko",
        identity: "Mozilla",
        versionSearch: "rv"
    },
    {         // for older Netscapes (4-)
        string: navigator.userAgent,
        subString: "Mozilla",
        identity: "Netscape",
        versionSearch: "Mozilla"
    }
],
dataOS : [
    {
        string: navigator.platform,
        subString: "Win",
        identity: "Windows"
    },
    {
        string: navigator.platform,
        subString: "Mac",
        identity: "Mac"
    },
    {
           string: navigator.userAgent,
           subString: "iPhone",
           identity: "iPhone/iPod"
    },
    {
        string: navigator.platform,
        subString: "Linux",
        identity: "Linux"
    }
 ]

};
BrowserDetect.init();
if (BrowserDetect.browser == 'Safari') {
    document.getElementById('applepay_show').style.display = 'block';
} else {
    document.getElementById('applepay_show').style.display = 'none';
};

  /* var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));
  if (isSafari) {
    $('.applepay-item').css('display', 'block');
  } */
  jQuery(document).ready(function(){
      $('input:radio[name="payment_method"]').change(function(){
          if (this.checked) {
            var method = this.value;
            if(method == "mastercard_applepay")
            {
                $('.apple_pay_content').css('display', 'block');
                return;
            } else {
                $('.apple_pay_content').css('display', 'none');
            }
            $('.visa_pay_content').each(function() {
              $( this ).remove();
            });
            $(this).parent().append('<div class="visa_pay_content" id="iframe_pay">{{ __('Operation is processing...') }}</div>')
            
            $.ajax({
                type: 'GET', //THIS NEEDS TO BE GET
                url: '/bills/payment_iframe/{{$bill->id}}/' + method+'/{{app()->getLocale()}}',
                beforeSend:function(){
                    if(method == "mastercard_iframe")
                    {
                        $('.loading').show();
                    }
                },
                success: function (data) {
                    $("#iframe_pay").html(data, function(){
                        $('.loading').hide();
                    });
                },
                complete:function(){
                },
                error: function() {
                     console.log(data);
                }
            });
          }
      });
  });



    /* if ( {{$bill->is_expired}} ) {
  $("#countdown").remove();
  $("#payment_method").remove();
  $("#back_btn").remove();
  $("#status").empty();
  $("#status").append('<div class="alert alert-danger" role="alert">{{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
} */


/* New countdown */
$(function(){
  let searchParams = new URLSearchParams(window.location.search)
  if(searchParams.has('print')){
    window.print();
  }

	$("#hm_timer").countdowntimer({
    minutes : {{ $bill->remaining_time_minutes}},
		seconds : {{ $bill->remaining_time_seconds}},
    size : "lg",
    timeUp : timeisUp
  });

  function timeisUp() {
    $("#new_countdown").remove();
    $("#payment_method").remove();
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
            $("#payment_method").remove();
            $("#back_btn").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
            break;
          case "canceled":
            $("#new_countdown").remove();
            $("#payment_method").remove();
            $("#back_btn").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-danger" role="alert">{{ __("this bill is canceled") }}</div>');
            break;
          case "expired":
            $("#new_countdown").remove();
            $("#payment_method").remove();
            $("#back_btn").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-danger" role="alert">{{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
            break;
          default:
            $("#payment_method").remove();
            $("#back_btn").remove();
            $("#status").empty();
            className = "badge-info";
        }
    });
</script>

{{-- APPLE PAY VIA MASTERCARD --}}
<script>
  <?php require app_path('Payment/Drivers/MasterCardApplePay/payment-request.js'); ?>
</script>
{{-- APPLE PAY VIA MASTERCARD --}}

    {!! JsValidator::formRequest('App\Http\Requests\PayBillRequest', '#bill_bay') !!}
@endpush
