(function($) {
  "use strict"; // Start of use strict
  
  // Scroll to top button appear
  $(document).on('scroll', function() {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
      $('.scroll-to-top').fadeIn();
    } else {
      $('.scroll-to-top').fadeOut();
    }
  });

  // Smooth scrolling using jQuery easing
  $(document).on('click', 'a.scroll-to-top', function(event) {
    $('html, body').animate({scrollTop: 0}, 500);
    event.preventDefault();
  });

})(jQuery); // End of use strict