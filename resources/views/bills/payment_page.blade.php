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
    <title>{{ __('Bill') . ' ' . $bill->number }} - SureBills</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <!-- App Css -->
    <link rel="stylesheet" href="/css/payment_page.css">
    <!-- Arabic Font -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <!-- Englesh Font -->
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,900&display=swap" rel="stylesheet">

    <!-- INCLUDE SESSION.JS JAVASCRIPT LIBRARY -->
    <script src="{{ config('payment.drivers.mastercard.base_url') }}/form/version/57/merchant/{{ config('payment.drivers.mastercard.merchant_id') }}/session.js"></script>
    <!-- APPLY CLICK-JACKING STYLING AND HIDE CONTENTS OF THE PAGE -->
    <style id="antiClickjack">body{display:none !important;}</style>
</head>
<body>

<div class="container" @if($bill->user->settings->api_bill_style && $bill->application_id) id="app" @endif >
    <div class="row align-items-center justify-content-center">
        <div class="col-12 @if($bill->user->settings->api_bill_style && $bill->application_id) col-md-4 @else col-md-12 @endif mt-4 p-0">
            <div class="pay_apple">
              <div class="load_form active"><div class="spinner-border text-muted"></div></div>
                @if($bill->user->settings->api_bill_style && $bill->application_id)
                    <div class="title rounded-top border bg-light p-2 d-flex align-items-center justify-content-between">
                        @if($bill->user->logo)
                            <div class="d-flex align-items-center justify-content-start">
                                <img src="{{ $bill->user->logo_url }}" alt="{{ $bill->user->business_name }}" class="rounded-circle" width="30px" height="30px">
                                <span class="mr-2 d-block">{{ $bill->user->business_name }}</span>
                            </div><!-- d-flex -->
                        @endif
                        @if($bill->application && $bill->is_redirect)
                            <a href="{{ $bill->back_url}}" title="{{ _('Back') }}" class="text-secondary">{{ _('Back') }}</a>
                        @endif
                    </div><!-- title -->
                    <span class="d-block font-weight-bold text-dark p-3 text-center border-right border-left">
                        {{ $bill->total }} {{ __('SAR') }}
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
                <div id="payment_area">
                    <div class="pay_button border-right border-left bg-light p-2 border-top" id="applepay_button">
                        <button id="payment" class="d-block mx-auto" lang="<?php echo App::getLocale() ?>" style="-webkit-appearance: -apple-pay-button; -apple-pay-button-type: buy; width: 230px; height: 40px; cursor: pointer; border-radius: 5px;"></button>
                    </div><!-- pay_button -->
                    <div class="pay_form border-right border-left border-bottom border-top rounded-bottom">
                        <div class="d-flex align-items-start justify-content-start  border-bottom">
                            <div class="icons p-3 d-flex align-items-center justify-content-between flex-column h-100 align-self-center">
                                <img src=" {{ asset('images/payment_page/mada.png')}}" class="d-block mx-auto mw-100" alt="#">
                                <img src=" {{ asset('images/payment_page/master.png')}}" class="d-block my-3 mx-auto mw-100" alt="#">
                                <img src=" {{ asset('images/payment_page/visa.png')}}" class="d-block mx-auto mw-100" alt="#">
                            </div><!-- icon -->
                            <div class="inputs">
                                <input type="text" id="card-number" class="input-field" title="{{ __('Card Number') }}" aria-label="enter your card number" placeholder="{{ __('Card Number') }}" value="" tabindex="1" dir="ltr" readonly>
                                <div class="tow_inputs">
                                    <span><input type="text" id="expiry-month" class="input-field expiry-month" title="{{ __('Expiry Month') }}" aria-label="two digit expiry month" placeholder="{{ __('Expiry Month') }}" value="" tabindex="2" dir="ltr" readonly></span>
                                    <span><input type="text" id="expiry-year" class="input-field" title="{{ __('Expiry Year') }}" aria-label="two digit expiry year" placeholder="{{ __('Expiry Year') }}" value="" tabindex="3" dir="ltr" readonly></span>
                                </div><!-- inputs -->
                                <input type="text" id="security-code" class="input-field security-code" title="{{ __('Security Code') }}" aria-label="three digit CCV security code" placeholder="{{ __('Security Code') }}" value="" tabindex="4" dir="ltr" readonly>
                                <input type="text" id="cardholder-name" class="input-field" title="{{ __('Cardholder Name') }}" aria-label="enter name on card" placeholder="{{ __('Cardholder Name') }}" value="" tabindex="5" dir="ltr" readonly>
                            </div><!-- inputs --> 
                        </div>
                        <div class="p-2">
                            @if($errors->any())
                                <div class="alert alert-danger" role="alert" id="errors">
                                    <ul>
                                        <li>{{ __($errors->first()) }}</li>
                                    </ul>
                                </div>
                            @else
                                <div class="alert alert-danger" role="alert" id="errors" style="display: none;">
                                    <ul></ul>
                                </div>
                            @endif
                            <button type="button" class="btn btn-success btn-block" id="payButton" onclick="pay('card');">
                                {{ __('Pay') }}
                            </button>
                        </div>
                    </div><!-- pay_form -->
                </div>
            </div><!-- pay_apple -->
        </div><!-- col-12 -->
    </div><!-- row -->
</div><!-- container -->
<br>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

@if($bill->user->settings->api_bill_style && $bill->application_id)
<script src="{{ asset('js/app.js') }}"></script>
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

{{--  MasterCard Hosted Session --}}
<?php require app_path('Payment/Drivers/MasterCardHostedSession/pay.js'); ?>

{{-- APPLE PAY VIA MASTERCARD --}}
<?php require app_path('Payment/Drivers/MasterCardApplePay/payment-request.js'); ?>
{{-- APPLE PAY VIA MASTERCARD --}}

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


{{-- Count Down --}}
@if($bill->user->settings->api_bill_style && $bill->application_id)
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
@endif
{{-- Count Down --}}


</body>
</html>
