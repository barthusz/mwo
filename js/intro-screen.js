jQuery(document).ready(function($) {
    var $slides = $('.intro-slide');
    var slideCount = $slides.length;
    var currentSlide = 0;

    // Only run slideshow if there are multiple slides
    if (slideCount > 1) {
        setInterval(function() {
            // Remove active class from current slide
            $slides.eq(currentSlide).removeClass('active');

            // Move to next slide
            currentSlide = (currentSlide + 1) % slideCount;

            // Add active class to next slide
            $slides.eq(currentSlide).addClass('active');
        }, 5000); // Change slide every 5 seconds
    }
});
