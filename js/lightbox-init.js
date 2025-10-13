// GLightbox initialization for Gutenberg galleries
document.addEventListener('DOMContentLoaded', function() {
    // Get theme options
    const showCaptions = typeof mwoOptions !== 'undefined' && mwoOptions.lightboxCaptions == 1;

    // Find all gallery images and wrap them with lightbox-enabled links
    const galleries = document.querySelectorAll('.wp-block-gallery');

    galleries.forEach(function(gallery, galleryIndex) {
        const images = gallery.querySelectorAll('.wp-block-image img, .blocks-gallery-item img');

        images.forEach(function(img, imgIndex) {
            // Check if image is not already wrapped in a link
            if (!img.parentElement.classList.contains('glightbox')) {
                const link = document.createElement('a');
                link.href = img.src;
                link.className = 'glightbox';
                link.setAttribute('data-gallery', 'gallery-' + galleryIndex);

                // Add caption if available and enabled
                if (showCaptions) {
                    // Check for data-caption attribute added by PHP
                    const captionText = img.getAttribute('data-caption');

                    if (captionText) {
                        link.setAttribute('data-description', captionText);
                    }
                }

                // Wrap image with link
                img.parentNode.insertBefore(link, img);
                link.appendChild(img);
            }
        });
    });

    // Initialize GLightbox
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        autoplayVideos: false,
        closeButton: true,
        closeOnOutsideClick: true,
        openEffect: 'fade',
        closeEffect: 'fade',
        slideEffect: 'slide',
        moreLength: 0,
        descPosition: 'right'
    });
});
