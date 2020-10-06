// =============================================
// Scroll To Top
// =============================================
$(document).ready(function(){
 $(window).scroll(function(){
  if ($(this).scrollTop() > 100) {
   $('.scrollup').addClass('active');
  } else {
   $('.scrollup').removeClass('active');
  }
 });
 $('.scrollup').click(function(){
  $("html, body").animate({ scrollTop: 0 }, 600); return false;
 });
});

// Owl Carousel
$(document).ready(function(){
  $('.how_work_slider').owlCarousel({
    items: 3,
    slideby: 3,
    autoplay: true,
    dots: true,
    margin: 0,
    lazyLoad: true,
    rtl:true,
    loop:false,
    responsive:{
      0:{items:1},
      480:{items:1},
      600:{items:2},
      1000:{items:3}
    }
  });
});


/* jQuery
================================================== */
$(function() {
  $('.acc__title').click(function(j) {
    
    var dropDown = $(this).closest('.acc__card').find('.acc__panel');
    $(this).closest('.acc').find('.acc__panel').not(dropDown).slideUp();
    
    if ($(this).hasClass('active')) {
      $(this).removeClass('active');
    } else {
      $(this).closest('.acc').find('.acc__title.active').removeClass('active');
      $(this).addClass('active');
    }
    
    dropDown.stop(false, true).slideToggle();
    j.preventDefault();
  });
});

// Smooth Scroll with jQuery
$('a[href*="#"]')
  // Remove links that don't actually link to anything
  .not('[href="#"]')
  .not('[href="#0"]')
  .click(function(event) {
    // On-page links
    if (
      location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
      && 
      location.hostname == this.hostname
    ) {
      // Figure out element to scroll to
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      // Does a scroll target exist?
      if (target.length) {
        // Only prevent default if animation is actually gonna happen
        event.preventDefault();
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000, function() {
          // Callback after animation
          // Must change focus!
          var $target = $(target);
          $target.focus();
          if ($target.is(":focus")) { // Checking if the target was focused
            return false;
          } else {
            $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
            $target.focus(); // Set focus again
          };
        });
      }
    }
  });