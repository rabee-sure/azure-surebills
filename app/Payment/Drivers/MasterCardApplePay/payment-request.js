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

const merchantId = 'merchant.bills.surepay.mastercard.applepay.sandbox';

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
        merchantIdentifier: merchantId,
        merchantCapabilities: ['supports3DS']
      }
    }
  ];

  let details = {
    displayItems: [{
      label: 'Original donation amount',
      amount: { currency: 'SAR', value: '2.00' }
    }],
    total: {
      label: 'Total due',
      amount: { currency: 'SAR', value : '2.00' }
    }
  };

  let options = {
    requestShipping: false,
    requestPayerEmail: false,
    requestPayerPhone: false,
    requestPayerName: false,
    shippingType: 'pickup'
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
        console.log(res);
        console.log(res.json());
        return res.json();
      } else {
        throw 'Merchant validation error.';
      }
    }).then((merchantSession) => {
      console.log(merchantSession);
      e.complete(merchantSession);
    });
  });

  let response;

  request.show().then(result => {
    response = result;
    switch (response.methodName) {
      case 'https://apple.com/apple-pay':
        console.log('This is Apple Pay JS');
        console.log(response);
        break;
      case 'https://bobpay.xyz/pay':
        console.log('This is Bobpay');
        console.log(response);
        break;
      case 'basic-card':
      default:
        console.log('This is basic-card');
        console.log(response);
        break;
    }
    // Emulate an interaction with a server
    setTimeout(() => {
      response.complete('success');
      alert('payment successfully complete!');
    }, 2000);
  }).catch(function(err) {
    if (err) {
      alert(`Could not make payment: ${err}`);
    }
    if (response) {
      response.complete('fail');
    }
  });
}

// Assuming an anchor is the target for the event listener.
window.addEventListener('DOMContentLoaded', function() {
  let button = document.querySelector('#payment');
  button.addEventListener('click', onBuyClicked);
});
