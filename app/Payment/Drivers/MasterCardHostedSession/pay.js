$('#payButton').on("click", function(e){
    loading();
});
// anticlickjack
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
  
                    //check if the security code was provided by the user
                    if (response.sourceOfFunds.provided.card.securityCode) {
                        // console.log("Security code was provided.");
                    }
                    let headers = new Headers({
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    });
                    fetch('/api/mastercard/handle-payment/', {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify({
                            billId: '<?php echo $bill->id; ?>',
                            paymentToken: response.session.id
                        })
                    }).then(response => response.json()).then(data => {
                        document.write('<style>body{margin: 0;} iframe{border: none;}</style>' + data.authentication.redirectHtml);
                    });
                } else if ("fields_in_error" == response.status)  {
                    if (response.errors.cardNumber) {
                        addError('Card number invalid or missing.');
                    }
                    if (response.errors.expiryYear) {
                        addError('Expiry year invalid or missing.');
                    }
                    if (response.errors.expiryMonth) {
                        addError('Expiry month invalid or missing.');
                    }
                    if (response.errors.securityCode) {
                        addError('Security code invalid.');
                    }
                } else if ("request_timeout" == response.status)  {
                    addError('Session update failed with request timeout: ' + response.errors.message);
                } else if ("system_error" == response.status)  {
                    addError('Session update failed with system error: ' + response.errors.message);
                }
            } else {
                addError('Session update failed: ' + response);
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
    PaymentSession.updateSessionFromForm('card');
}
