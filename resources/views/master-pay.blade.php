@extends('layouts.bill')
<style id="antiClickjack">body{display:none !important;}</style>

<div class="container" dir="ltr">
    <div class='row'>
        <div class='col-12 form-group'>
            <input class="form-control" placeholder="{{__('Card Number')}}" type="text" id="card-number" class="input-field" title="{{__('Card Number')}}" aria-label="enter your card number" value="" tabindex="1" readonly>
        </div>
        <div class='col-4 form-group'>
            <input class="form-control" placeholder="{{__('cvv')}}" type="text" id="security-code" class="input-field" title="{{__('cvv')}}" aria-label="three digit CCV security code" value="" tabindex="4" readonly>
        </div>
        <div class='col-4 form-group'>
            <input class="form-control" placeholder="{{__('expiration month')}}" type="text" id="expiry-month" class="input-field" title="{{__('expiration month')}}" aria-label="two digit expiry month" value="" tabindex="2" readonly>
        </div>
        <div class='col-4 form-group'>
            <input class="form-control" placeholder="{{__('expiration year')}}" type="text" id="expiry-year" class="input-field" title="{{__('expiration year')}}" aria-label="two digit expiry year" value="" tabindex="3" readonly>
        </div>
        <div class='col-12 form-group required'>
        <input class="form-control" placeholder="{{__('name on card')}}" type="text" id="cardholder-name" class="input-field" title="{{__('name on card')}}" aria-label="enter name on card" value="" tabindex="5" readonly>
        </div>
    <div class='col-12 form-group'>
    <button class='form-control btn btn-primary' id="payButton" onclick="pay('card');">{{__('Pay')}}</button>
    </div>
 </div>
</div>

<style>
    input{
        text-align: {{Config::get('app.locale') == 'en'? 'left': 'right'}};
    }
</style>

<script src="{{config('payment.drivers.mastercard_iframe.session_script')}}?debug=true"></script>
{{-- <script src="https://test-gateway.mastercard.com/static/threeDS/1.3.0/three-ds.min.js"></script> --}}

<script type="text/javascript">
console.log("{{$sessionId}}")
if (self === top) {
    var antiClickjack = document.getElementById("antiClickjack");
    antiClickjack.parentNode.removeChild(antiClickjack);
} else {
    top.location = self.location;
}

PaymentSession.configure({
    fields: {
        // Attach hosted fields to your payment page
            card: {
                number: "#card-number",
                securityCode: "#security-code",
                expiryMonth: "#expiry-month",
                expiryYear: "#expiry-year",
                nameOnCard: "#cardholder-name"
            },
          },
          session:"{{$sessionId}}",

    frameEmbeddingMitigation: ["javascript"],
    callbacks: {
        initialized: function(response) {
            // HANDLE INITIALIZATION RESPONSE
            if (response.status === "ok") {
                console.log(response);
            }
        },

        formSessionUpdate: function(response) {
            // HANDLE RESPONSE FOR UPDATE SESSION
            // console.log(response);
        if (response.status) {
            if ("ok" == response.status) {
                console.log("Session updated with data: " + response.session.id);


                //check if the security code was provided by the user
                if (response.sourceOfFunds.provided.card.securityCode) {
                    console.log("Security code was provided.");
                }
                console.log(response.sourceOfFunds.provided.card.scheme);
                //check if the user entered a MasterCard credit card
                if (response.sourceOfFunds.provided.card.scheme == 'MASTERCARD') {
                    console.log("The user entered a MasterCard credit card.")
                }
            } else if ("fields_in_error" == response.status)  {

                console.log("Session update failed with field errors.");
                if (response.errors.cardNumber) {
                    console.log("Card number invalid or missing.");
                }
                if (response.errors.expiryYear) {
                    console.log("Expiry year invalid or missing.");
                }
                if (response.errors.expiryMonth) {
                    console.log("Expiry month invalid or missing.");
                }
                if (response.errors.securityCode) {
                    console.log("Security code invalid.");
                }
                if (response.errors.number) {
                    console.log("Gift card number invalid or missing.");
                }
                if (response.errors.pin) {
                    console.log("Pin invalid or missing.");
                }
                if (response.errors.bankAccountHolder) {
                    console.log("Bank account holder invalid.");
                }
                if (response.errors.bankAccountNumber) {
                    console.log("Bank account number invalid.");
                }
                if (response.errors.routingNumber) {
                    console.log("Routing number invalid.");
                }
            } else if ("request_timeout" == response.status)  {
                console.log("Session update failed with request timeout: " + response.errors.message);
            } else if ("system_error" == response.status)  {
                console.log("Session update failed with system error: " + response.errors.message);
            }
        } else {
            console.log("Session update failed: " + response);
        }
        },
        visaCheckout: function(response) {
            console.log(response);
            // HANDLE VISA CHECKOUT RESPONSE
        },
        amexExpressCheckout: function(response) {
            console.log(response);
           // HANDLE AMEX EXPRESS CHECKOUT RESPONSE
        }
    },
    interaction: {
        displayControl: {
            formatCard: "EMBOSSED",
            invalidFieldCharacters: "REJECT"
        }
    },
    // order: {
    //     amount: 101.00,
    //     currency: "SAR",
    //     description: "Invoice number: 1003",
    //     id: 665,
    // },
    // transaction: {
    //     id: 10741,
    // },
    // apiOperation: 'CREATE_CHECKOUT_SESSION',
    // interaction: {
    //     operation: 'PURCHASE'
    // },
    // authentication: {
    //     transactionId: 998
    // },

});

function pay(paymentType) {
    // PaymentSession.initialized();
    // UPDATE THE SESSION WITH THE INPUT FROM HOSTED FIELDS
    PaymentSession.updateSessionFromForm(paymentType);
}
</script>
