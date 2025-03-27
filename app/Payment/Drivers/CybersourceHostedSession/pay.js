// anticlickjack
if (self === top) {
    var antiClickjack = document.getElementById("antiClickjack");
    antiClickjack.parentNode.removeChild(antiClickjack);
} else {
    top.location = self.location;
}


function completePayment(extraHeaders = {}, extraBody = {}) {
    // Clear previous validation errors
    clearValidationErrors();
    loading();

    // Call the payerAuthSteps function
    payerAuthSteps({}, extraBody);
}

function payerAuthSteps(extraHeaders = {}, extraBody = {}){
    // Call the payerAuthSetup function
    // console.log("payerAuthSteps");

    fetch("<?php echo route('cybersource.payerAuth.setup') ?>", {
        method: "POST",
        headers: requestHeader(extraHeaders),
        body: requestPayload(extraBody)
    }).then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    }).then(data => { // Equivalent to success
        if (data.payerAuthSetupRes) {
            // console.log(data.payerAuthSetupRes);
            BuildDeviceDataCollectionIFrame(data.payerAuthSetupRes.consumerAuthenticationInformation.accessToken);
            setTimeout(function() {
                extraBody.payerAuthReferenceId = data.payerAuthSetupRes.consumerAuthenticationInformation.referenceId;
                checkEnrollment({}, extraBody);
            }, 10000);
        }
    }).catch(error => {
        if (error.errors) {
            displayValidationErrors(error.errors);
        }
    });
}

function BuildDeviceDataCollectionIFrame(accessToken) {
    let actionUrl = "<?php echo config('cybersource.device_data_collection_action_url'); ?>"
    let iframe = "<iframe id='cardinal_collection_iframe' name='collectionIframe' height='10' width='10' style='display: none;'></iframe> <form id='cardinal_collection_form' method='POST' target='collectionIframe' action='"+actionUrl+"'> <input id='cardinal_collection_form_input' type='hidden' name='JWT' value='"+accessToken+"'> </form>";
    $("#payerAuthIFrames").html(iframe);

    var cardinalCollectionForm = document.querySelector('#cardinal_collection_form');
        if (cardinalCollectionForm) // form exists 
            cardinalCollectionForm.submit();

    window.addEventListener("message", function (event) {
        if (event.origin === actionUrl) {
            // console.log(event.data);
        }
    }, false);
}

function checkEnrollment(extraHeaders = {}, extraBody = {}) {
    // Call the checkEnrollment function
    // console.log("checkEnrollment");

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
            // console.log(data.payerAuthCheckEnrollmentRes);
            if(data.payerAuthCheckEnrollmentRes.status == "PENDING_AUTHENTICATION"){
                loaded();
                BuildStepUpIFrame(data.payerAuthCheckEnrollmentRes.consumerAuthenticationInformation.accessToken);
                // window.location.href = '<?php echo rtrim(env("APP_URL"), "/") ?>/iframe-2/'+data.payerAuthCheckEnrollmentRes.consumerAuthenticationInformation.accessToken;
            }
            setTimeout(function() {
                loading();
                emptyIFrame();
                extraBody.authenticationTransactionId = data.payerAuthCheckEnrollmentRes.consumerAuthenticationInformation.authenticationTransactionId;
                validateAuthentication({}, extraBody);
            }, 30000);
        }
    }).catch(error => {
        if (error.errors) {
            displayValidationErrors(error.errors);
        }
    });
}

function BuildStepUpIFrame(accessToken) {
    let actionUrl = "<?php echo config('cybersource.payer_auth_setup_url'); ?>"
    let iframe = "<iframe name='step-up-iframe' width='460' height='400'></iframe> <form id='step-up-form' target='step-up-iframe' method='post' action='"+actionUrl+"'> <input type='hidden' name='JWT' value='"+accessToken+"' /> <input type='hidden' name='MD' value='optionally_include_custom_data_that_will_be_returned_as_is' /> </form>";
    $("#payerAuthIFrames").html(iframe);

    var stepUpForm = document.querySelector('#step-up-form');
        if (stepUpForm) // Step-Up form exists
            stepUpForm.submit();
}

function emptyIFrame() {
    $("#payerAuthIFrames").html('');
}

function validateAuthentication(extraHeaders = {}, extraBody = {}) {
    // Call the validateAuthentication function
    // console.log("validateAuthentication");

    fetch("<?php echo route('cybersource.payerAuth.validation.results') ?>", {
        method: "POST",
        headers: requestHeader(extraHeaders),
        body: requestPayload(extraBody)
    }).then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    }).then(data => { // Equivalent to success
        // // console.log(data);
        if (data.payerAuthValidationRes) {
            if(data.payerAuthValidationRes.status == "AUTHENTICATION_SUCCESSFUL"){
                // console.log(data.payerAuthValidationRes.consumerAuthenticationInformation);
                extraBody.authenticationResult = data.payerAuthValidationRes.consumerAuthenticationInformation.authenticationResult;
                extraBody.authenticationStatusMsg = data.payerAuthValidationRes.consumerAuthenticationInformation.authenticationStatusMsg;
                extraBody.cavv = data.payerAuthValidationRes.consumerAuthenticationInformation.cavv;
                extraBody.xid = data.payerAuthValidationRes.consumerAuthenticationInformation.xid;
                extraBody.eciRaw = data.payerAuthValidationRes.consumerAuthenticationInformation.eciRaw;
                // console.log(extraBody);
                completePaymentProcess({}, extraBody);
            }
        }
    }).catch(error => {
        if (error.errors) {
            displayValidationErrors(error.errors);
        }
    });
}

function completePaymentProcess(extraHeaders = {}, extraBody = {}) {
    // Call the completePaymentProcess function
    // console.log("completePaymentProcess");

    // Complete the payment process
    fetch("<?php echo route('process.payment') ?>", {
        method: "POST",
        headers: requestHeader(extraHeaders),
        body: requestPayload(extraBody)
    }).then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    }).then(data => { // Equivalent to success
        if (data.redirect_to) {
            window.location.href = data.redirect_to;
        }
        else {
            location.reload();
        }
    }).catch(error => {
        if (error.errors) {
            displayValidationErrors(error.errors);
        }
    }).finally(() => { // Equivalent to complete
        loaded();
        enableSubmitButton();
    });
}


function requestHeader(extraHeaders = {}) {
    let headers = { "Content-Type": "application/json", "X-Pay-Time": "<?php echo $payTime ?>", "X-Bill-Signature": "<?php echo $billSignature ?>" };
    return { ...headers, ...extraHeaders };
}

function requestPayload(extraBody = {}) {
    let body = { "billId": "<?php echo $bill->id ?>" }
    return JSON.stringify({ ...body, ...extraBody });
}

// Function to display validation errors from Laravel
function displayValidationErrors(errors) {
    var errorMessage = '<ul>';
    Object.keys(errors).forEach(field => {
        errorMessage += '<li>' + errors[field].join(', ') + '</li>';
    });
    errorMessage += '</ul>';
    $('#error-message').html(errorMessage).removeClass('d-none');
}

// Function to display custom validation error
function displayCustomeValidationError(error) {
    var errorMessage = '<ul>';
    errorMessage += '<li>' + error + '</li>';
    errorMessage += '</ul>';
    $('#error-message').html(errorMessage).removeClass('d-none');
}

// Function to clear previous validation errors
function clearValidationErrors() {
    $('#error-message').addClass('d-none').empty();
}

function formFormDataToObject(formData) {
    let object = {};
    formData.forEach((value, key) => {
        if (object[key]) {
            if (!Array.isArray(object[key])) {
                object[key] = [object[key]];
            }
            object[key].push(value);
        } else {
            object[key] = value;
        }
    });
    return object;
}

function disableBackButton() {
    $("#back_btn a").on("click.disabled", function (event) {
        event.preventDefault();
    }).css("color", "gray");
}

function enableBackButton() {
    $("#back_btn a").off("click.disabled").removeAttr('style');
}

function disableSubmitButton() {
    var submitButton = document.querySelector("button[type='submit']");
    submitButton.disabled = true;
}

function enableSubmitButton() {
    var submitButton = document.querySelector("button[type='submit']");
    submitButton.removeAttribute('disabled');
}