jQuery(document).ready(function($) {
    // Initialize Masonry for Gutenberg galleries
    var galleries = $('.wp-block-gallery, .blocks-gallery-grid, .wp-block-gallery.has-nested-images');

    if (galleries.length) {
        galleries.each(function() {
            var $gallery = $(this);
            var masonryInstance = null;

            // Hybrid lazy loading: first 12 images eager, rest lazy
            $gallery.find('img').each(function(index) {
                if (index < 12) {
                    $(this).attr('loading', 'eager');
                } else {
                    $(this).attr('loading', 'lazy');
                }
            });

            // Check if image has valid dimensions (critical for cached images)
            function hasValidDimensions(img) {
                return img.complete && img.naturalWidth > 0 && img.naturalHeight > 0;
            }

            // Force browser reflow to update dimensions
            function forceReflow() {
                $gallery.find('img').each(function() {
                    void this.offsetHeight; // Force reflow
                });
            }

            // Initialize or layout Masonry
            function initOrLayout() {
                if (!masonryInstance) {
                    masonryInstance = $gallery.masonry({
                        itemSelector: '.wp-block-image, .blocks-gallery-item',
                        percentPosition: true,
                        gutter: 8,
                        horizontalOrder: false,
                        transitionDuration: '0.3s'
                    });
                } else {
                    $gallery.masonry('layout');
                }
            }

            // Check if images are ready and initialize
            function checkAndInit() {
                var allReady = true;
                $gallery.find('img').each(function() {
                    if (!hasValidDimensions(this)) {
                        allReady = false;
                        return false;
                    }
                });

                if (allReady) {
                    forceReflow();
                    initOrLayout();
                    // Extra layout after short delay for cached images
                    setTimeout(function() {
                        if (masonryInstance) $gallery.masonry('layout');
                    }, 100);
                } else {
                    // Wait and try again
                    setTimeout(checkAndInit, 10);
                }
            }

            // Start initialization
            checkAndInit();

            // Backup: use imagesLoaded for any late-loading images
            $gallery.imagesLoaded({ background: false })
                .progress(function() {
                    initOrLayout();
                })
                .always(function() {
                    forceReflow();
                    initOrLayout();
                });

            // Handle lazy-loaded images
            $gallery.find('img[loading="lazy"]').each(function() {
                $(this).on('load', function() {
                    if (masonryInstance) $gallery.masonry('layout');
                });
            });

            // IntersectionObserver for lazy images
            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    var needsLayout = false;
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) needsLayout = true;
                    });
                    if (needsLayout && masonryInstance) {
                        $gallery.masonry('layout');
                    }
                }, { rootMargin: '50px' });

                $gallery.find('.wp-block-image, .blocks-gallery-item').each(function() {
                    observer.observe(this);
                });
            }
        });
    }

    // Window resize handler
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            galleries.each(function() {
                if ($(this).data('masonry')) {
                    $(this).masonry('layout');
                }
            });
        }, 250);
    });

    // Scroll handler for lazy-loaded images
    var scrollTimer;
    $(window).on('scroll', function() {
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(function() {
            galleries.each(function() {
                if ($(this).data('masonry')) {
                    $(this).masonry('layout');
                }
            });
        }, 150);
    });
});
