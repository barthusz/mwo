// GLightbox initialization for Gutenberg galleries
document.addEventListener('DOMContentLoaded', function() {
    // Find all gallery images and wrap them with lightbox-enabled links
    const galleries = document.querySelectorAll('.wp-block-gallery');

    galleries.forEach(function(gallery) {
        const images = gallery.querySelectorAll('.wp-block-image img, .blocks-gallery-item img');

        images.forEach(function(img) {
            // Check if image is not already wrapped in a link
            if (!img.parentElement.classList.contains('glightbox')) {
                const link = document.createElement('a');
                link.href = img.src;
                link.className = 'glightbox';
                link.setAttribute('data-gallery', 'gallery-' + gallery.dataset.galleryId || 'gallery');

                // Add caption if available
                const figcaption = img.closest('figure')?.querySelector('figcaption');
                if (figcaption) {
                    link.setAttribute('data-glightbox', 'description: ' + figcaption.textContent);
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
        moreLength: 0
    });
});
