@extends('layouts.bill')

@section('title', __('Bill') . ' ' . $bill->number)

@section('content')


<div class="single_bill_page">
  <div class="container" >

    <div class="row  justify-content-center">
      <div class="col-12 col-md-8 col-lg-6 col-xl-6">
        <div class="single_bill_content">
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
            <span>{{ $bill->business_name }}</span>

            @if(isset($bill->user->settings->header_bill))
              <p>{{ $bill->user->settings->header_bill }}</p>
            @endif

            <p>{{  $bill->user->business_address}}</p>
            <b>{{  $bill->user->business_mobile }}</b>
          </div><!-- title -->

          @if($bill->application_id && !$bill->is_expired)
            <div class="countdown" id="new_countdown">
              <p>{{ __('the bill will expire in')}}</p>
              @if($bill->remaining_time_hours == '00')
                <span id="hm_timer" ></span>
              @else
                <span>{{$bill->remaining_time_hours}} {{__('Hour')}}</span>
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
                <div id="applepay_show" class="item">
                    <input type="radio" id="apple_pay" name="payment_method" value="hyperpay_applepay">
                    <label for="apple_pay">
                    <p>{{ __('Apple Pay') }}</p>
                    <div class="icon_apple"></div>
                    <div class="checkmark"></div>
                    </label>
                </div><!-- item -->
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
          <a href="/" title="Sure Bills" class="logo_bills"></a>
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

/* if ( {{$bill->is_expired}} ) {
  $("#countdown").remove();
  $("#payment_method").remove();
  $("#back_btn").remove();
  $("#status").empty();
  $("#status").append('<div class="alert alert-danger" role="alert">{{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
} */


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
    $("#payment_method").remove();
    // $("#back_btn").remove();
    $("#status").empty();
    $("#status").append('<div class="alert alert-danger" role="alert">{{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
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
    {!! JsValidator::formRequest('App\Http\Requests\PayBillRequest', '#bill_bay') !!}
@endpush
