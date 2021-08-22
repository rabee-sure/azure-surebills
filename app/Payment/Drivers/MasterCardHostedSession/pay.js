$('#payButton').on("click", function(e){
    loading();
});
// anticlickjack
if (self === top) {
    var antiClickjack = document.getElementById("antiClickjack");
    antiClickjack.parentNode.removeChild(antiClickjack);
} else {
    console.log('bug bug bug');
    var antiClickjack = document.getElementById("antiClickjack");
    antiClickjack.parentNode.removeChild(antiClickjack);
    // top.location = self.location;
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

                    // handle 3ds process
                    let headers = new Headers({
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    });
                    fetch('/api/mastercard/handle-payment/', {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify({
                            billId: '<?php echo $bill->id; ?>',
                            paymentToken: response.session.id,
                            from_iframe: '<?php echo request()->from_iframe; ?>',
                        })
                    }).then(response => response.json()).then(data => {

                        if (data.error) {
                            alert(data.error);
                            // window.location = data.redirect;
                        }
                        // parse html
                        let redirectHtml = $( '<div></div>' );
                        redirectHtml.html(data.authentication.redirectHtml);

                        // change form target to be in the same page
                        redirectHtml.find('form').attr('target', '_self');

                        // append redirect form
                        $( "body" ).append(redirectHtml);
                    });

                } else if ("fields_in_error" == response.status)  {
                    if (response.errors.cardNumber) {
                        addError("<?php echo __('Card number invalid or missing.') ?>");
                    }
                    if (response.errors.expiryMonth) {
                        addError("<?php echo __('Expiry month invalid or missing, example: 02') ?>");
                    }
                    if (response.errors.expiryYear) {
                        addError("<?php echo __('Expiry year invalid or missing, example: 26') ?>");
                    }
                    if (response.errors.securityCode) {
                        addError("<?php echo __('Security code invalid.') ?>");
                    }
                } else if ("request_timeout" == response.status)  {
                    addError("<?php echo __('Session update failed with request timeout:') ?> " + response.errors.message);
                } else if ("system_error" == response.status)  {
                    addError("<?php echo __('Session update failed with system error:') ?> " + response.errors.message);
                }
            } else {
                addError("<?php echo __('Session update failed:') ?> " + response);
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
