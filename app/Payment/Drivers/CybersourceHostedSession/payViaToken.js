document.addEventListener("DOMContentLoaded", function () {
    const captureContext = "<?php echo $microformSessionToken ?>";
    const flex = new Flex(captureContext);
    const microform = flex.microform();
    
    // Initialize card number and CVV fields
    initializeFields(microform);

    // Set up form submission handling
    setupFormSubmission(microform);
});

// Function to initialize input fields
function initializeFields(microform) {
    createField(microform, 'number', '#card-number-container', "xxxx xxxx xxxx xxxx");
    createField(microform, 'securityCode', '#cvv-container', "CVV");
}

// Function to create and load a specific field
function createField(microform, type, container, placeholder) {
    const field = microform.createField(type, { placeholder: placeholder });
    field.load(container);
}

// Function to handle form submission
function setupFormSubmission(microform) {
    document.getElementById("payment-form").addEventListener("submit", function (event) {
        event.preventDefault();
        disableSubmitButton();
        processPayment(microform);
    });
}

// Function to generate payment token
function processPayment(microform) {
    const options = {
        expirationMonth: document.querySelector('#exp_month').value,
        expirationYear: document.querySelector('#exp_year').value
    };

    // Generate the token for payment processing
    microform.createToken(options, function (err, token) {
        if (err) {
            enableSubmitButton()
            displayCustomeValidationError("<?php echo trans('Payment Faild') ?>");
            return;
        }

        completePayment({"X-Pay-Token": token}, {})
    });
}