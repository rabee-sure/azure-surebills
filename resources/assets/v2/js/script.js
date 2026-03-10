// =============================================
// Sidebar Toggle
// =============================================
let sidebarHide = localStorage.getItem('sidebarHide'); 
var sidebarToggle = document.querySelector('.sidebarButton');
var enableSidebarHide = () => {
  document.body.classList.add('sidebar_hide');
  localStorage.setItem('sidebarHide', 'enabled');
}
var disableSidebarHide = () => {
  document.body.classList.remove('sidebar_hide');
  localStorage.setItem('sidebarHide', null);
}
if (sidebarHide === 'enabled') {
  enableSidebarHide();
}
sidebarToggle.addEventListener('click', () => {
  sidebarHide = localStorage.getItem('sidebarHide'); 
  if (sidebarHide !== 'enabled') {
    enableSidebarHide();
  } else {  
    disableSidebarHide(); 
  }
});

// =============================================
// Sidebar Toggle Phone
// =============================================
$(".sidebarBtnMobile").click(function () {
  $("body").toggleClass("sidebarOpen");
});

// =============================================
// Bootstrap Tooltip
// =============================================
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
$(document).ready(function() {
  $("body").tooltip({ selector: '[data-bs-toggle="tooltip"]' });
});

// =============================================
// Showing Body
// =============================================
$("body > *").css({ opacity: 0 });
setTimeout(function () {
  $("body").removeClass("show-spinner");
  $("body > *").animate({ opacity: 1 }, 200);
}, 400);

// =============================================
// convert Arabic number to English in input tel
// ============================================= 
function toEnglishNumber2(strNum2) {
  var ar = '٠١٢٣٤٥٦٧٨٩'.split('');
  var en = '0123456789'.split('');
  strNum2 = strNum2.replace(/[٠١٢٣٤٥٦٧٨٩]/g, x => en[ar.indexOf(x)]);
  strNum2 = strNum2.replace(/[^\d.]/g, '');
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
  $('.formBtn').on('click', function() {
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
      $this.attr('disabled', false);
    }, 5000);
  });
});

// =============================================
// Select2 Plugin
// ============================================= 
$(document).ready(function() {
  $('.select2-single').select2();
});