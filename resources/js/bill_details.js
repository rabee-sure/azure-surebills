 $(".bill_payment input[type='radio']").on("change", function() {
  // Regardless of WHICH radio was clicked, is the
  //  showSelect radio active?
   if ($("#visa_pay").is(':checked')) {
     $('.visa_pay_content').removeClass("d-none");
   } else {
     $('.visa_pay_content').addClass("d-none");
   }
 })




 var card = new Card({
  form: 'form',
  container: '.card-wrapper',
  placeholders: {
    number: '**** **** **** ****',
    name: 'Full Name',
    expiry: '**/****',
    cvc: '***'
}
});