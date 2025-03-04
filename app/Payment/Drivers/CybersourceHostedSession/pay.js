// anticlickjack
if (self === top) {
    var antiClickjack = document.getElementById("antiClickjack");
    antiClickjack.parentNode.removeChild(antiClickjack);
} else {
    top.location = self.location;
}


function completePayment(extraHeaders = {}, extraBody = {}) {
    clearValidationErrors();
    loading();
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