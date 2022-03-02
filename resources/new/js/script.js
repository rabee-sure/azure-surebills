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