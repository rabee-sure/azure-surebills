document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("payment-form").addEventListener("submit", function (e) {
        e.preventDefault();
        let body = formFormDataToObject(new FormData(this));
        completePayment({}, body)
    });
});