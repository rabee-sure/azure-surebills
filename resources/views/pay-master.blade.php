<html>
<head>
<!-- INCLUDE SESSION.JS JAVASCRIPT LIBRARY -->
<script src="https://cibpaynow.gateway.mastercard.com/form/version/57/merchant/TEST3000000330/session.js"></script>
<script src="{{config('payment.drivers.mastercard_iframe.api_base_url')}}"
        data-error="errorCallback"
        data-cancel="cancelCallback"
        data-complete="completeCallback"
        ></script>
<!-- APPLY CLICK-JACKING STYLING AND HIDE CONTENTS OF THE PAGE -->
<style id="antiClickjack">body{display:none !important;}</style>
</head>
<body>

<!-- CREATE THE HTML FOR THE PAYMENT PAGE -->

<div>Please enter your payment details {{config('payment.drivers.mastercard_iframe.api_base_url')}}:</div>
<h3>Credit Card</h3>
<div>Card Number: <input type="text" id="card-number" class="input-field" title="card number" aria-label="enter your card number" value="" tabindex="1" readonly></div>
<div>Expiry Month:<input type="text" id="expiry-month" class="input-field" title="expiry month" aria-label="two digit expiry month" value="" tabindex="2" readonly></div>
<div>Expiry Year:<input type="text" id="expiry-year" class="input-field" title="expiry year" aria-label="two digit expiry year" value="" tabindex="3" readonly></div>
<div>Security Code:<input type="text" id="security-code" class="input-field" title="security code" aria-label="three digit CCV security code" value="" tabindex="4" readonly></div>
<div>Cardholder Name:<input type="text" id="cardholder-name" class="input-field" title="cardholder name" aria-label="enter name on card" value="" tabindex="5" readonly></div>
<div><button id="payButton" onclick="pay('card');">Pay Now</button></div>
<!-- JAVASCRIPT FRAME-BREAKER CODE TO PROVIDE PROTECTION AGAINST IFRAME CLICK-JACKING -->

<script type="text/javascript">
if (self === top) {
    var antiClickjack = document.getElementById("antiClickjack");
    antiClickjack.parentNode.removeChild(antiClickjack);
} else {
    top.location = self.location;
}
Checkout.showLightbox();
function completeCallback(resultIndicator, sessionVersion) {
      console.log('session: '+ resultIndicator);
      console.log('session: '+ sessionVersion);
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
            giftCard: {
                    number: "#gift-card-number",
                    pin: "#gift-card-pin"
                  },
            ach: {
                    accountType: "#ach-account-type",
                    bankAccountHolder: "#ach-account-holder",
                    bankAccountNumber: "#ach-account-number",
                    routingNumber: "#ach-routing-number"
                  }
          },
    frameEmbeddingMitigation: ["javascript"],
    callbacks: {
        initialized: function(response) {
            // HANDLE INITIALIZATION RESPONSE
            if (response.status === "ok") {
                document.getElementById("visaCheckoutButton").style.display = 'block';
            }
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
            // HANDLE VISA CHECKOUT RESPONSE
        },
        amexExpressCheckout: function(response) {
           // HANDLE AMEX EXPRESS CHECKOUT RESPONSE
        }
    },
    interaction: {
        displayControl: {
            formatCard: "EMBOSSED",
            invalidFieldCharacters: "REJECT"
        }
    },
    order: {
        amount: 10.00,
        currency: "SAR"
    },
    wallets: {
        visaCheckout: {
            enabled: true,
            // Add Visa Checkout API specific attributes here
            countryCode: "AU",
            displayName: "Display name",
            locale: "en_au",
            logoUrl: "http://logo.logo",
            payment: {
                cardBrands: [
                    "VISA"
                ]
            },
            review: {
                buttonAction: "Pay",
                message: "Message"
            },
            shipping: {
                acceptedRegions: [
                    "AU"
                ],
                collectShipping: true
            }
        },
        amexExpressCheckout: {
        enabled: true,
        // Add Amex Express Checkout API specific attributes here
        initTags: {
        "theme": "responsive",
        "env": "qa",
        "disable_btn": "false",
        "button_color": "light",
        "client_id": "<MSO Client Id from the Amex Express Checkout configuration page in Merchant Administration>",
        }
    }
}
});

function pay(paymentType) {
    // UPDATE THE SESSION WITH THE INPUT FROM HOSTED FIELDS
    if (paymentType === 'giftCard') {
        PaymentSession.updateSessionFromForm(paymentType, '<localCardBrand>');
    } else {
        PaymentSession.updateSessionFromForm(paymentType);
    }
}
</script>
</body>
</html>
