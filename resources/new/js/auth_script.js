mydir = $("html").attr("dir");
if (mydir == 'rtl') {
  rtlVal=true
} else {
  rtlVal=false
}
$(document).ready(function() {
  $('.authSlider').slick({
    lazyLoad: 'ondemand',
    infinite: true,
    rtl: rtlVal,
    arrows: false,
    autoplay: true,
    autoplaySpeed: 4000,
  });
});

// =============================================
// convert Arabic number to English in input tel
// ============================================= 
function toEnglishNumber2(strNum2) {
  var ar = '٠١٢٣٤٥٦٧٨٩'.split('');
  var en = '0123456789'.split('');
  strNum2 = strNum2.replace(/[٠١٢٣٤٥٦٧٨٩]/g, x => en[ar.indexOf(x)]);
  strNum2 = strNum2.replace(/[^\d]/g, '');
  return strNum2;
}
$(document).on('keyup', 'input[type="tel"]', function(e) {
  var val = toEnglishNumber2($(this).val())
  $(this).val(val)
  this.dispatchEvent(new Event('input'));
});

// =============================================
// Loading Spinner Buttton
// ============================================= 
$(document).ready(function() {
  $('.login_button').on('click', function() {
    var $this = $(this);
    var loadingText = '<i class="fad fa-spinner fa-spin"></i>';
    if ($(this).html() !== loadingText) {
      $this.data('original-text', $(this).html());
      $this.html(loadingText);
    }
    $(this).attr('disabled', 'disabled');
    $(this).parents('form').submit();
    setTimeout(function() {
      $this.html($this.data('original-text'));
    }, 5000);
  });
});