//    Copyright 2017 Google
//
//    Licensed under the Apache License, Version 2.0 (the "License");
//    you may not use this file except in compliance with the License.
//    You may obtain a copy of the License at
//
//        http://www.apache.org/licenses/LICENSE-2.0
//
//    Unless required by applicable law or agreed to in writing, software
//    distributed under the License is distributed on an "AS IS" BASIS,
//    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//    See the License for the specific language governing permissions and
//    limitations under the License.

if (window.ApplePaySession && ApplePaySession.canMakePayments()) {
    document.getElementById('applepay_button').style.display = 'block';
} else {
    document.getElementById('applepay_button').style.display = 'none';
}

function onBuyClicked(event) {
  if (!PaymentRequest) {
    return;
  }
  // Payment Request API is available.
  // Stop the default anchor redirect.
  event.preventDefault();

  let supportedInstruments = [
    {
      supportedMethods: 'https://apple.com/apple-pay',
      data: {
        supportedNetworks: [
          'mada', 'masterCard', 'visa'
        ],
        version: 3,
        countryCode: 'SA',
        merchantIdentifier: "<?php echo env('MASTERCARD_APPLEPAY_MERCHANT_ID'); ?>",
        merchantCapabilities: ['supports3DS']
      }
    }
  ];

  let details = {
    displayItems: [{
      label: "<?php echo __('Bill No.'); ?> #<?php echo $bill->number; ?>",
      amount: { currency: 'SAR', value: parseFloat("<?php echo $bill->total; ?>").toFixed(2)}
    }],
    total: {
      label: "<?php echo __('Total'); ?>",
      amount: {currency: 'SAR', value: parseFloat("<?php echo $bill->total; ?>").toFixed(2)}
    }
  };

  let options = {
    requestShipping: false,
    requestPayerEmail: false,
    requestPayerPhone: false,
    requestPayerName: false,
    shippingType: 'storePickup'
  };

  // Initialization
  let request = new PaymentRequest(supportedInstruments, details, options);

  request.addEventListener('merchantvalidation', e => {
    let headers = new Headers({
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    });
    fetch('/api/applepay/validate/', {
      method: 'POST',
      headers: headers,
      body: JSON.stringify({validationURL: e.validationURL})
    }).then(res => {
      if (res.status === 200) {
        var resJson = res.json();
        return resJson;
      } else {
        throw 'Merchant validation error.';
      }
    }).then((merchantSession) => {
      e.complete(merchantSession);
    });
  });

  let response;

  request.show().then(result => {
    response = result;
    loading();
    let headers = new Headers({
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    });
    fetch('/api/applepay/check-payment/', {
      method: 'POST',
      headers: headers,
      body: JSON.stringify({billId: '<?php echo $bill->id; ?>', paymentToken: response.details.token.paymentData})
    }).then(response => response.json()).then(data => {
      if (data.error && data.error != '') {
        alert(`Could not make payment data: ${data.error}`);
        console.log(data);
        location.reload();
        response.complete('fail');
      } else {
        response.complete('success');
        window.location = data.redirect;
      }
    });
  }).catch(function(err) {
      console.log(err);
    // if (err) {
    //   alert(`Could not make payment err: ${err}`);
    //   console.log(err);
    //   console.error("Uh oh, something bad happened", err.message);
    //   // location.reload();
    //   response.complete('fail');
    // }
  });
}

// Assuming an anchor is the target for the event listener.
window.addEventListener('DOMContentLoaded', function() {
  let button = document.querySelector('#payment');
  button.addEventListener('click', onBuyClicked);
});
