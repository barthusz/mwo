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
        descPosition: 'right',
        zoomable: false
    });

    // Add fullscreen button to lightbox
    function addFullscreenButton() {
        const lightboxContainer = document.querySelector('.glightbox-container');
        if (!lightboxContainer || document.querySelector('.gfullscreen-btn')) {
            return; // Already exists or container not found
        }

        // Create fullscreen button
        const fullscreenBtn = document.createElement('button');
        fullscreenBtn.className = 'gfullscreen-btn';
        fullscreenBtn.setAttribute('aria-label', 'Enter fullscreen');
        // Simple expand icon matching the original design
        fullscreenBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 7V3C2 2.44772 2.44772 2 3 2H7M13 2H17C17.5523 2 18 2.44772 18 3V7M18 13V17C18 17.5523 17.5523 18 17 18H13M7 18H3C2.44772 18 2 17.5523 2 17V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        // Add click handler
        fullscreenBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // REMOVE the button from DOM completely
            fullscreenBtn.remove();

            // Then go fullscreen
            toggleFullscreen(lightboxContainer);
        });

        // Find the toolbar and add button
        const closeBtn = lightboxContainer.querySelector('.gclose');
        if (closeBtn && closeBtn.parentNode) {
            closeBtn.parentNode.insertBefore(fullscreenBtn, closeBtn);
        } else {
            lightboxContainer.appendChild(fullscreenBtn);
        }
    }

    // Toggle fullscreen function
    function toggleFullscreen(element) {
        if (!document.fullscreenElement && !document.webkitFullscreenElement &&
            !document.mozFullScreenElement && !document.msFullscreenElement) {
            // Enter fullscreen
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen();
            } else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if (element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
        } else {
            // Exit fullscreen
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    }

    // Add fullscreen button when lightbox opens
    lightbox.on('open', function() {
        setTimeout(addFullscreenButton, 100);
    });

    // Apply content protection to lightbox if enabled
    if (document.body.classList.contains('content-protection-enabled')) {
        lightbox.on('open', function() {
            // Wait a moment for the lightbox to fully render
            setTimeout(function() {
                const lightboxContainer = document.querySelector('.glightbox-container');
                if (lightboxContainer) {
                    // Prevent right-click on entire lightbox container
                    lightboxContainer.addEventListener('contextmenu', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }, true);

                    // Prevent drag on images
                    const lightboxImages = lightboxContainer.querySelectorAll('img');
                    lightboxImages.forEach(function(img) {
                        img.addEventListener('contextmenu', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }, true);
                        img.addEventListener('dragstart', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }, true);
                    });
                }
            }, 100);
        });

        // Re-apply protection on slide change
        lightbox.on('slide_changed', function() {
            setTimeout(function() {
                const lightboxContainer = document.querySelector('.glightbox-container');
                if (lightboxContainer) {
                    const lightboxImages = lightboxContainer.querySelectorAll('img');
                    lightboxImages.forEach(function(img) {
                        img.addEventListener('contextmenu', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }, true);
                        img.addEventListener('dragstart', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }, true);
                    });
                }
            }, 100);
        });
    }
});
