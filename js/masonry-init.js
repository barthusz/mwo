jQuery(document).ready(function($) {
    // Initialize Masonry for Gutenberg galleries
    var galleries = $('.wp-block-gallery, .blocks-gallery-grid, .wp-block-gallery.has-nested-images');

    if (galleries.length) {
        galleries.each(function() {
            var $gallery = $(this);

            // Wait for images to load
            $gallery.imagesLoaded(function() {
                $gallery.masonry({
                    itemSelector: '.wp-block-image, .blocks-gallery-item',
                    columnWidth: '.wp-block-image, .blocks-gallery-item',
                    percentPosition: true,
                    gutter: 8,
                    horizontalOrder: true
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
                $(this).masonry('layout');
            });
        }, 250);
    });
});
