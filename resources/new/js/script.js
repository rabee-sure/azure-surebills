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


var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})