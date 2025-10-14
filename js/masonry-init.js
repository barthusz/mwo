// Wait for WINDOW LOAD (not document ready) - ensures all images are loaded
jQuery(window).on('load', function($) {
    $ = jQuery;

    // Initialize Masonry for Gutenberg galleries
    var galleries = $('.wp-block-gallery, .blocks-gallery-grid, .wp-block-gallery.has-nested-images');

    if (galleries.length) {
        galleries.each(function() {
            var $gallery = $(this);

            // Initialize Masonry immediately since all images are loaded
            var masonryInstance = $gallery.masonry({
                itemSelector: '.wp-block-image, .blocks-gallery-item',
                percentPosition: true,
                gutter: 8,
                horizontalOrder: false,
                transitionDuration: '0.3s'
            });

            // Extra safety: layout after short delay
            setTimeout(function() {
                $gallery.masonry('layout');
            }, 100);

            // Handle window resize
            var resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    $gallery.masonry('layout');
                }, 250);
            });
        });
    }
});
