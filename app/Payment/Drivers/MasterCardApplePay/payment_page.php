<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SureBills</title>
    <style>
        #applePay {
            width: 150px;  
            height: 50px;  
            display: none;   
            border-radius: 5px;    
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
            background-image: -webkit-named-image(apple-pay-logo-white); 
            background-position: 50% 50%;
            background-color: black;
            background-size: 60%; 
            background-repeat: no-repeat;  
        }
    </style>
</head>
<body>
<div>
<button type="button" id="applePay"></button>
<p style="display:none" id="got_notactive">ApplePay is possible on this browser, but not currently activated.</p>
<p style="display:none" id="notgot">ApplePay is not available on this browser</p>
<p style="display:none" id="success">Test transaction completed, thanks.</p>
</div>
<script type="text/javascript">

var debug = true;

if (window.ApplePaySession) {
   var merchantIdentifier = 'merchant.bills.surepay.mastercard.applepay.sandbox';
   var promise = ApplePaySession.canMakePaymentsWithActiveCard(merchantIdentifier);
   promise.then(function (canMakePayments) {
      if (canMakePayments) {
         document.getElementById("applePay").style.display = "block";
         logit('hi, I can do ApplePay');
      } else {   
         document.getElementById("got_notactive").style.display = "block";
         logit('ApplePay is possible on this browser, but not currently activated.');
      }
    }); 
} else {
    logit('ApplePay is not available on this browser');
    document.getElementById("notgot").style.display = "block";
}

document.getElementById("applePay").onclick = function(evt) {

    var paymentRequest = {
        currencyCode: 'SAR',
        countryCode: 'SA',
        lineItems: [
            {label: 'A', amount: 1},
            {label: 'B', amount: 1.5}
        ],
        total: {
            label: 'Invoice number: 1000044444',
            amount: 2.5
        },
        supportedNetworks: ['amex', 'masterCard', 'visa', 'mada'],
        merchantCapabilities: ['supports3DS']
    };
        
    var session = new ApplePaySession(1, paymentRequest);
        
    // Merchant Validation
    session.onvalidatemerchant = function (event) {
        logit(event);
        var promise = performValidation(event.validationURL);
        promise.then(function (merchantSession) {
            session.completeMerchantValidation(merchantSession);
        }); 
    }

    function performValidation(valURL) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var data = JSON.parse(this.responseText);
                logit(data);
                resolve(data);
            };
            xhr.onerror = reject;
            xhr.open('GET', '/api/applepay/validate/' + valURL);
            xhr.send();
        });
    }
    
    session.onpaymentauthorized = function (event) {

        logit('starting session.onpaymentauthorized');
        logit('NB: This is the first stage when you get the *full shipping address* of the customer, in the event.payment.shippingContact object');
        logit(event);

        var promise = sendPaymentToken(event.payment.token);
        promise.then(function (success) {   
            var status;
            if (success){
                status = ApplePaySession.STATUS_SUCCESS;
                document.getElementById("applePay").style.display = "none";
                document.getElementById("success").style.display = "block";
            } else {
                status = ApplePaySession.STATUS_FAILURE;
            }
            
            logit( "result of sendPaymentToken() function =  " + success );
            session.completePayment(status);
        });
    }

    function sendPaymentToken(paymentToken) {
        return new Promise(function(resolve, reject) {
            logit('starting function sendPaymentToken()');
            logit(paymentToken);
            
            logit("this is where you would pass the payment token to your third-party payment provider to use the token to charge the card. Only if your provider tells you the payment was successful should you return a resolve(true) here. Otherwise reject;");
            logit("defaulting to resolve(true) here, just to show what a successfully completed transaction flow looks like");
            if ( debug == true )
            resolve(true);
            else
            reject;
        });
    }
    
    session.oncancel = function(event) {
        logit('starting session.cancel');
        logit(event);
    }
    
    session.begin();
};
    
function logit( data ){
    if( debug == true ){
        console.log(data);
    }
};
</script>
</body>
</html>
