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
      countryCode: 'EG',
      merchantIdentifier: "<?php echo env('CYBERSOURCE_APPLEPAY_MERCHANT_ID'); ?>",
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
  shippingType: 'pickup'
};

// Initialization
let request = new PaymentRequest(supportedInstruments, details, options);

let extraHeaders = {};
let extraBody = {};

request.addEventListener('merchantvalidation', e => {
  let headers = new Headers({
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  });
  fetch("<?php echo route('applepay.validate') ?>", {
    method: 'POST',
    headers: headers,
    body: JSON.stringify({validationURL: e.validationURL, host: host})
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

const updatedDetails = {
  total: {
    label: "<?php echo __('Total'); ?>",
    amount: {currency: 'SAR', value: parseFloat("<?php echo $bill->total; ?>").toFixed(2)}
  }
};

request.show(updatedDetails).then(result => {
  response = result;
  loading();

  extraBody.paymentToken = response.details.token.paymentData;
  extraBody.applePay = true;

  fetch("<?php echo route('cybersource.payerAuth.setup') ?>", {
    method: 'POST',
    headers: requestHeader(extraHeaders),
    body: requestPayload(extraBody)
  }).then(
    response => response.json()
  ).then(
    data => {
    if (data.error && data.error != '') {
      // alert(`test Could not make payment data: ${data.error}`);
      // console.log(data);
      // location.reload();
      response.complete('fail');
    } else {
      if (data.payerAuthSetupRes) {
        BuildDeviceDataCollectionIFrame(data.payerAuthSetupRes.consumerAuthenticationInformation.accessToken);
        setTimeout(function() {
          extraBody.payerAuthReferenceId = data.payerAuthSetupRes.consumerAuthenticationInformation.referenceId;
          checkEnrollment({}, extraBody);
        }, 10000);
      }
    }
  });
}).catch(function(err) {
 
    if (err) {
      // alert(`I Could not make payment err: ${err}`);
      // location.reload();
      response.complete('fail');
    }
  });
}

// Assuming an anchor is the target for the event listener.
window.addEventListener('DOMContentLoaded', function() {
  let button = document.querySelector('#payment');
  button.addEventListener('click', onBuyClicked);
});

function BuildDeviceDataCollectionIFrame(accessToken) {
  let actionUrl = "<?php echo config('cybersource.device_data_collection_action_url'); ?>"
  let iframe = "<iframe id='cardinal_collection_iframe' name='collectionIframe' height='10' width='10' style='display: none;'></iframe> <form id='cardinal_collection_form' method='POST' target='collectionIframe' action='"+actionUrl+"'> <input id='cardinal_collection_form_input' type='hidden' name='JWT' value='"+accessToken+"'> </form>";
  $("#payerAuthIFrames").html(iframe);

  var cardinalCollectionForm = document.querySelector('#cardinal_collection_form');
      if (cardinalCollectionForm) // form exists 
          cardinalCollectionForm.submit();

  window.addEventListener("message", function (event) {
      if (event.origin === actionUrl) {
          // 
      }
  }, false);
}

function checkEnrollment(extraHeaders = {}, extraBody = {}) {
  // Call the checkEnrollment function
  fetch("<?php echo route('cybersource.payerAuth.enrollment.check') ?>", {
      method: "POST",
      headers: requestHeader(extraHeaders),
      body: requestPayload(extraBody)
  }).then(response => {
      if (!response.ok) {
          return response.json().then(err => { throw err; });
      }
      return response.json();
  }).then(data => { // Equivalent to success
      if (data.payerAuthCheckEnrollmentRes) {
          if(data.payerAuthCheckEnrollmentRes.status == "PENDING_AUTHENTICATION"){
              loaded();
              window.location.href = '<?php echo rtrim(env("INVOICE_SUBDOMAIN_URL"), "/") ?>/payment/otp/'+data.payerAuthCheckEnrollmentRes.consumerAuthenticationInformation.accessToken;
          }
      }
  }).catch(error => {
      if (error.errors) {
          displayValidationErrors(error.errors);
      }
  }).finally(() => { // Equivalent to complete
    response.complete('success');
    loaded();
  });

  function requestHeader(extraHeaders = {}) {
      let headers = { "Content-Type": "application/json", "X-Pay-Time": "<?php echo $payTime ?>", "X-Bill-Signature": "<?php echo $billSignature ?>" };
      return { ...headers, ...extraHeaders };
  }

  function requestPayload(extraBody = {}) {
      let body = { "billId": "<?php echo $bill->id ?>" }
      return JSON.stringify({ ...body, ...extraBody });
  }
}
