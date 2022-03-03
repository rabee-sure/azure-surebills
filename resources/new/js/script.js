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
  $("body > *").animate({ opacity: 1 }, 100);
}, 300);