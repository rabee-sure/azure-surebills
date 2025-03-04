<!DOCTYPE html>
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if IE 9 ]><html class="ie ie9" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="{{ App::getLocale() }}" dir="{{ App::isLocale('ar') ? 'rtl' : 'ltr' }}">
<!--<![endif]-->
<head>
    <!-- Basic Page Needs -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ __('Bill No.') . ' ' . $bill->number }} - SureBills</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <!-- App Css -->
    <link rel="stylesheet" href="{{asset('css/payment_page.css')}}">

    <style>
        .rtl {
          direction: rtl !important;
        }
        
        @font-face {
          font-family: "A Jannat LT";
          src: url("{{rtrim(config('payment.invoice_subdomain_url'), '/')}}/fonts/AJannatLT-Bold/AJannatLT-Bold_1.ttf") format("truetype");
          font-weight: normal;
          font-style: normal;
        }
        
        .riyal-symbol-font {
          font-family: "A Jannat LT", sans-serif;
        }
        .gap-1 {
        gap: .25rem !important;
      }
      .fw-bold {
        font-weight: bold !important;
      }
    </style>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">

    @if(config('payment.default_payment_gateway') != 'cybersource')
        <!-- INCLUDE SESSION.JS JAVASCRIPT LIBRARY -->
        <script src="{{ config('payment.drivers.mastercard.base_url') }}/form/version/57/merchant/{{ config('payment.drivers.mastercard.merchant_id') }}/session.js"></script>
        <!-- APPLY CLICK-JACKING STYLING AND HIDE CONTENTS OF THE PAGE -->
    @endif

    <style id="antiClickjack">body{display:none !important;}</style>
</head>
<body>

  <div @if($bill->user->settings->api_bill_style && $bill->application_id) class="payment-api-page" id="app" @endif>
    <div class="pay_apple">
      <div class="load_form active">
        <div class="spinner-border text-muted"></div>
      </div>
      @if($bill->user->settings->api_bill_style && $bill->application_id)
        <div class="title rounded-top border bg-light p-2 d-flex align-items-center justify-content-between">
            @if($bill->user->logo)
                <div class="d-flex align-items-center justify-content-start">
                    <img src="{{ $bill->user->logo_url }}" alt="{{ $bill->user->business_name }}" class="rounded-circle" width="30px" height="30px">
                    <span class="mr-2 d-block">{{ $bill->user->business_name }}</span>
                </div><!-- d-flex -->
            @endif
            @if($bill->application && $bill->is_redirect)
                <a href="{{ $bill->back_url}}" title="{{ __('Back') }}" class="text-secondary">{{ __('Back') }}</a>
            @endif
        </div><!-- title -->
        <span class="d-block font-weight-bold text-dark p-3 text-center border-right border-left rtl">
            {{ $bill->total }} <span class="riyal-symbol-font">$</span>
            @if(!$bill->is_expired && $bill->remaining_time_hours['hours'] == '00' && $bill->remaining_time_hours['days'] == 0)
                <div class="countdown" id="new_countdown">
                    <p>{{ __('the bill will expire in')}}</p>
                    <span id="hm_timer"></span>
                </div><!-- countdown -->
            @endif
        </span> 
      @endif
      @if($bill->user->settings->api_bill_style && $bill->application_id)
          <div id="status">
          </div>
      @endif
      <div id="payment_area" @if($bill->user->settings->api_bill_style && $bill->application_id) class="p-2 border-right border-left border-bottom" @endif>
        @if (!isset($sureEasyRendrer))  
        <div class="card mb-3" id="applepay_button">
          <div class="card-header p-3 d-flex align-items-center justify-content-between">
            <span class="d-block flex-grow-1 fw-bold">{{ trans('Pay With Your Apple Pay') }}</span>
            <div class="payments-icons flex-shrink-0 d-flex align-items-center justify-content-end gap-2">
              <img src="{{ asset('images/payment_page/payment-3.webp') }}" alt="applepay">
            </div><!-- payments-icons -->
          </div><!-- card-header -->
          <div class="card-body p-3">
            <div class="pay_button">
              <button id="payment" class="d-block mx-auto" lang="<?php echo App::getLocale() ?>" style="-webkit-appearance: -apple-pay-button; -apple-pay-button-type: buy; width: 230px; height: 40px; cursor: pointer; border-radius: 5px;"></button>
            </div><!-- pay_button -->
          </div><!-- card-body -->
        </div><!-- card -->
        @endif
        @include($payForm)
      </div>
    </div><!-- pay_apple -->
  </div><!-- payment-api-page -->

  @stack('footer-scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
      var host = "{{isset($host) ? $host : request()->getHost()}}";
    </script>

    @if($bill->user->settings->api_bill_style && $bill->application_id)
        <script src="{{ asset('js/app.js') }}"></script>

        {{-- Count Down --}}
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
                $("#status").empty();
                $("#status").append('<div class="alert alert-danger" role="alert">{{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
              }
            });
            /* New countdown */
        </script>
        {{-- Count Down --}}
    @endif

    <script>
        function loading() {
            $('#errors').css('display', 'none');
            $("#errors ul").html('');
            $(".load_form").addClass('active');
        }
        function loaded() {
            $(".load_form").removeClass('active');
        }
        function addError(error) {
            $('#errors').css('display', 'block');
            $('#errors ul').append('<li>' + error + '</li>');
            loaded();
        }
        // Loadin Page
        $(window).on("load",function(){
            loaded();
        });

        @if(config('payment.default_payment_gateway') != 'cybersource')
            {{--  MasterCard Hosted Session --}}
            <?php require app_path('Payment/Drivers/MasterCardHostedSession/pay.js'); ?>
            @if (!isset($sureEasyRendrer))
                {{-- APPLE PAY VIA MASTERCARD --}}
                <?php require app_path('Payment/Drivers/MasterCardApplePay/payment-request.js'); ?>
                {{-- APPLE PAY VIA MASTERCARD --}}
            @endif
        @else 
            {{--  Cybersource Hosted Session --}}
            <?php require app_path('Payment/Drivers/CybersourceHostedSession/pay.js'); ?>
            @if($microformSessionToken)
                <?php require app_path('Payment/Drivers/CybersourceHostedSession/payViaToken.js'); ?>
            @else 
                <?php require app_path('Payment/Drivers/CybersourceHostedSession/payViaCard.js'); ?>
            @endif
              @if (!isset($sureEasyRendrer))
                {{-- APPLE PAY VIA Cybersource --}}
                <?php require app_path('Payment/Drivers/CybersourceApplePay/payment-request.js'); ?>
                {{-- APPLE PAY VIA Cybersource --}}
              @endif
        @endif
        
        {{-- Socket Update --}}
        @if($bill->user->settings->api_bill_style && $bill->application_id)
        Echo.channel('bill.{{$bill->id}}').listen('BillStatusUpdated', (e) => {
            var className;

            switch(e.bill.status) {
                case "pending":
                    className = "badge-info";
                    break;
                case "paid":
                    $("#payment_area").remove();
                    $("#status").empty();
                    $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
                    break;
                case "canceled":
                    $("#payment_area").remove();
                    $("#status").empty();
                    $("#status").append('<div class="alert alert-danger" role="alert">{{ __("this bill is canceled") }}</div>');
                    break;
                case "expired":
                    $("#payment_area").remove();
                    $("#status").empty();
                    $("#status").append('<div class="alert alert-danger" role="alert">{{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
                    break;
                default:
                    $("#payment_area").remove();
                    $("#status").empty();
            }
        });
        @endif
        {{-- Socket Update --}}
    </script>

</body>
</html>
