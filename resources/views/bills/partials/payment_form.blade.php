@push('css_styles')
  @if(config('payment.default_payment_gateway') != 'cybersource')
    <!-- INCLUDE SESSION.JS JAVASCRIPT LIBRARY -->
    <script src="{{ config('payment.drivers.mastercard.base_url') }}/form/version/57/merchant/{{ config('payment.drivers.mastercard.merchant_id') }}/session.js"></script>
    <!-- APPLY CLICK-JACKING STYLING AND HIDE CONTENTS OF THE PAGE -->
  @endif
  <style id="antiClickjack">body{display:none !important;}</style>
  <link rel="stylesheet" href="{{ asset('assets/v2/css/payment_form.css') }}">
@endpush


<div id="payment_area">
  @if (!isset($sureEasyRendrer))
    <div class="pay_button" id="applepay_button">
      <button id="payment" class="d-block mx-auto" lang="<?php echo App::getLocale() ?>" style="-webkit-appearance: -apple-pay-button; -apple-pay-button-type: buy; width: 230px; height: 40px; cursor: pointer; border-radius: 5px;"></button>
    </div><!-- pay_button -->
  @endif
  <div class="pay_form">
    <div class="form_area">
      <div class="payment_icons">
        <img src=" {{ asset('assets/v2/img/payments/mada_lg.png')}}" class="d-block mw-100" alt="#">
        <img src=" {{ asset('assets/v2/img/payments/mastercard_lg.png')}}" class="d-block mw-100" alt="#">
        <img src=" {{ asset('assets/v2/img/payments/visa_lg.png')}}" class="d-block mw-100" alt="#">
      </div><!-- payment_icons -->
      <div class="inputs">
        <input type="text" id="card-number" class="input-field" title="{{ __('Card Number') }}" aria-label="enter your card number" placeholder="{{ __('Card Number') }}" value="" tabindex="1" dir="ltr" readonly>
        <div class="two_inputs">
          <span><input type="text" id="expiry-month" class="input-field expiry-month" title="{{ __('Expiry Month') }}" aria-label="two digit expiry month" placeholder="{{ __('Expiry Month') }}" value="" tabindex="2" dir="ltr" readonly></span>
          <span><input type="text" id="expiry-year" class="input-field" title="{{ __('Expiry Year') }}" aria-label="two digit expiry year" placeholder="{{ __('Expiry Year') }}" value="" tabindex="3" dir="ltr" readonly></span>
        </div><!-- two_inputs -->
        <input type="text" id="security-code" class="input-field security-code" title="{{ __('Security Code') }}" aria-label="three digit CCV security code" placeholder="{{ __('Security Code') }}" value="" tabindex="4" dir="ltr" readonly>
        <input type="text" id="cardholder-name" class="input-field" title="{{ __('Cardholder Name') }}" aria-label="enter name on card" placeholder="{{ __('Cardholder Name') }}" value="" tabindex="5" dir="ltr" readonly>
      </div><!-- inputs -->
    </div><!-- form_area -->
    <div class="p-2 d-flex flex-column gap-2">
        @if(isset($errors) && $errors->any())
        <div class="mb-6">
          <div class="alert alert-danger m-0" role="alert" id="errors">{{ __($errors->first()) }}</div>
        @else
          <div id="errors" style="display: none;">
            <ul class="list-group"></ul>
          </div>
        @endif
        <button type="button" class="btn btn-success btn-block" id="payButton" onclick="pay('card');">
            {{ __('Pay') }}
        </button>
    </div>
  </div><!-- pay_form -->
</div><!-- payment_area -->

  @push('footer-scripts')
    <script>
      var host = "{{isset($host) ? $host : request()->getHost()}}";
    </script>
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
            $('#errors ul').append('<li class="list-group-item list-group-item-danger">' + error + '</li>');
            loaded();
        }
        // Loadin Page
        $(window).on("load",function(){
            loaded();
        });

        {{--  MasterCard Hosted Session --}}
        <?php require app_path('Payment/Drivers/MasterCardHostedSession/pay.js'); ?>

        {{-- APPLE PAY VIA MASTERCARD --}}
        @if (!isset($sureEasyRendrer))
        <?php require app_path('Payment/Drivers/MasterCardApplePay/payment-request.js'); ?>
        @endif
        {{-- APPLE PAY VIA MASTERCARD --}}

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


    </script>
  @endpush

</body>
</html>
