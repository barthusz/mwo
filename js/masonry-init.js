jQuery(document).ready(function($) {
    // Initialize Masonry for Gutenberg galleries
    var galleries = $('.wp-block-gallery, .blocks-gallery-grid, .wp-block-gallery.has-nested-images');

    if (galleries.length) {
        galleries.each(function() {
            var $gallery = $(this);
            var masonryInstance = null;

            // Wait for images to load
            $gallery.imagesLoaded(function() {
                masonryInstance = $gallery.masonry({
                    itemSelector: '.wp-block-image, .blocks-gallery-item',
                    percentPosition: true,
                    gutter: 8,
                    horizontalOrder: false, // Changed to false for better vertical distribution
                    transitionDuration: '0.3s',
                    initLayout: true,
                    // Ensure proper fitting
                    stagger: 30
                });

                // Force layout after a short delay to catch any late-loading images
                setTimeout(function() {
                    $gallery.masonry('layout');
                }, 100);

                // Relayout after each image loads (catches lazy-loaded images)
                $gallery.imagesLoaded().progress(function() {
                    $gallery.masonry('layout');
                });
            });
        });
    }

    // Reinitialize on window resize (debounced)
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            galleries.each(function() {
                var $gallery = $(this);
                if ($gallery.data('masonry')) {
                    $gallery.masonry('layout');
                }
            });
        }, 250);
    });

    // Also layout on scroll end (catches any lazy-loaded images)
    var scrollTimer;
    $(window).on('scroll', function() {
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(function() {
            galleries.each(function() {
                var $gallery = $(this);
                if ($gallery.data('masonry')) {
                    $gallery.masonry('layout');
                }
            });
        }, 150);
    });
});
