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
        @if(isset($errors) && $errors->any())
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