<!DOCTYPE html>
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if IE 9 ]><html class="ie ie9" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="{{ App::getLocale() }}" dir="{{ App::getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<!--<![endif]-->
<head>
    <!-- Basic Page Needs -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ __('Bill') . ' ' . $bill->number }} - SureBills</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- App Css -->
    <link rel="stylesheet" href="/payment_page/css/app.css">
    <!-- Arabic Font -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <!-- Englesh Font -->
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,900&display=swap" rel="stylesheet">

    <!-- INCLUDE SESSION.JS JAVASCRIPT LIBRARY -->
    <script src="{{ config('payment.drivers.mastercard.base_url') }}/form/version/57/merchant/{{ config('payment.drivers.mastercard.merchant_id') }}/session.js"></script>
    <!-- APPLY CLICK-JACKING STYLING AND HIDE CONTENTS OF THE PAGE -->
    <style id="antiClickjack">body{display:none !important;}</style>
</head>
<body class="hidden_overflow">

<div id="loading_page"></div>

<div class="container">
    <div class="row align-items-center justify-content-center">
        <div class="col-12 col-md-4">
            <div class="pay_apple mt-4">
                <div class="title rounded-top border bg-light p-2 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center justify-content-start">
                        <img src="https://codesign.com.bd/conversations/content/images/2020/03/yahoo_default_logo.png" alt="#" class="rounded-circle" width="30px" height="30px">
                        <span class="mr-2 d-block">Shawarma Classic</span>
                    </div><!-- d-flex -->
                    <a href="#" title="Cancel" class="text-secondary">Cancel</a>
                </div><!-- title -->
                <span class="d-block font-weight-bold text-dark p-3 text-center border-right border-left" dir="ltr">120.00 SAR</span> 
                <div class="pay_button border bg-light p-2">
                  <a href="#" title="#"><img src="/payment_page/images/apple-pay.png" width="250px" class="d-block mx-auto" alt="#"></a>
                </div><!-- pay_button -->
                <div class="pay_form border-right border-left border-bottom rounded-bottom">
                    <div class="d-flex align-items-start justify-content-start  border-bottom">
                        <div class="icons p-3 d-flex align-items-center justify-content-between flex-column h-100 align-self-center">
                            <img src="/payment_page/images/mada.png" class="d-block mx-auto mw-100" alt="#">
                            <img src="/payment_page/images/master.png" class="d-block my-3 mx-auto mw-100" alt="#">
                            <img src="/payment_page/images/visa.png" class="d-block mx-auto mw-100" alt="#">
                        </div><!-- icon -->
                        <div class="inputs border-left">
                          <input type="text" id="card-number" class="form-control border-top-0 border-right-0 border-left-0 rounded-0 px-2 shadow-none" title="Card Number" aria-label="enter your card number" placeholder="card number" value="" tabindex="1" readonly>
                          <div class="d-flex align-items-center justify-content-start">
                            <input type="text" id="expiry-month" class="form-control border-top-0 border-left-0 rounded-0 px-2 shadow-none" width="50%" title="expiry month" aria-label="two digit expiry month" placeholder="Expiry Month" value="" tabindex="2" readonly>
                            <input type="text" id="expiry-year" class="form-control border-top-0 border-right-0 border-left-0 rounded-0 px-2 shadow-none" title="expiry year" aria-label="two digit expiry year" placeholder="Expiry Year" width="50%" value="" tabindex="3" readonly>
                          </div><!-- inputs -->
                          <input type="text" id="security-code" class="form-control border-right-0 border-left-0 border-top-0 border-bottom rounded-0 px-2 shadow-none" title="security code" aria-label="three digit CCV security code" placeholder="Security Code" value="" tabindex="4" readonly>
                          <input type="text" id="cardholder-name" class="form-control border-0 rounded-0 px-2 " title="cardholder name" aria-label="enter name on card" placeholder="Cardholder Name" value="" tabindex="5" readonly>
                        </div><!-- inputs --> 
                    </div>
                    <div class="p-2">
                        <button type="button" class="btn btn-success btn-block" id="payButton" onclick="pay('card');">Pay</button>
                    </div>
                </div><!-- pay_form -->
            </div><!-- pay_apple -->
        </div><!-- col-12 -->
    </div><!-- row -->
</div><!-- container -->

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
// Loadin Page
$(window).on("load",function(){$("body").removeClass('hidden_overflow')});

if (self === top) {
    var antiClickjack = document.getElementById("antiClickjack");
    antiClickjack.parentNode.removeChild(antiClickjack);
} else {
    top.location = self.location;
}

PaymentSession.configure({
    fields: {
        // ATTACH HOSTED FIELDS TO YOUR PAYMENT PAGE FOR A CREDIT CARD
        card: {
            number: "#card-number",
            securityCode: "#security-code",
            expiryMonth: "#expiry-month",
            expiryYear: "#expiry-year",
            nameOnCard: "#cardholder-name"
        }
    },
    //SPECIFY YOUR MITIGATION OPTION HERE
    frameEmbeddingMitigation: ["javascript"],
    callbacks: {
        initialized: function(response) {
            // HANDLE INITIALIZATION RESPONSE
        },
        formSessionUpdate: function(response) {
            // HANDLE RESPONSE FOR UPDATE SESSION
            if (response.status) {
                if ("ok" == response.status) {
                    console.log("Session updated with data: " + response.session.id);
  
                    //check if the security code was provided by the user
                    if (response.sourceOfFunds.provided.card.securityCode) {
                        console.log("Security code was provided.");
                    }
  
                    //check if the user entered a Mastercard credit card
                    if (response.sourceOfFunds.provided.card.scheme == 'MASTERCARD') {
                        console.log("The user entered a Mastercard credit card.")
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
                } else if ("request_timeout" == response.status)  {
                    console.log("Session update failed with request timeout: " + response.errors.message);
                } else if ("system_error" == response.status)  {
                    console.log("Session update failed with system error: " + response.errors.message);
                }
            } else {
                console.log("Session update failed: " + response);
            }
        }
    },
    interaction: {
        displayControl: {
            formatCard: "EMBOSSED",
            invalidFieldCharacters: "REJECT"
        }
    }
 });

function pay() {
    // UPDATE THE SESSION WITH THE INPUT FROM HOSTED FIELDS
    PaymentSession.updateSessionFromForm('card');
}
</script>

</body>
</html>
