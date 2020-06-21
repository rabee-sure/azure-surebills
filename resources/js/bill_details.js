 $(".bill_payment input[type='radio']").on("change", function() {
  // Regardless of WHICH radio was clicked, is the
  //  showSelect radio active?
   if ($("#visa_pay").is(':checked')) {
     $('.visa_pay_content').removeClass("d-none");
   } else {
     $('.visa_pay_content').addClass("d-none");
   }
 })




 $('form').card({
  // a selector or DOM element for the container
  // where you want the card to appear
  container: '.card-wrapper', // *required*

  // all of the other options from above
});