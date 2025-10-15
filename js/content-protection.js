/**
 * Content Protection Script
 * Protects images from downloading via right-click, drag, and keyboard shortcuts
 */

(function() {
    'use strict';

    // Prevent right-click on images (including lightbox)
    document.addEventListener('contextmenu', function(e) {
        if (e.target.tagName === 'IMG' &&
            (e.target.closest('.wp-block-gallery') ||
             e.target.closest('.wp-block-image') ||
             e.target.closest('.site-content') ||
             e.target.closest('.glightbox-container') ||
             e.target.closest('.gslide-image'))) {
            e.preventDefault();
            return false;
        }
    });

    // Prevent drag and drop on images (including lightbox)
    document.addEventListener('dragstart', function(e) {
        if (e.target.tagName === 'IMG' &&
            (e.target.closest('.wp-block-gallery') ||
             e.target.closest('.wp-block-image') ||
             e.target.closest('.site-content') ||
             e.target.closest('.glightbox-container') ||
             e.target.closest('.gslide-image'))) {
            e.preventDefault();
            return false;
        }
    });

    // Detect save shortcuts (Ctrl+S / Cmd+S)
    document.addEventListener('keydown', function(e) {
        // Ctrl+S or Cmd+S
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            console.log('Content is protected. Saving is disabled.');
            return false;
        }

        // Ctrl+U or Cmd+U (View Source) - just log, don't prevent
        if ((e.ctrlKey || e.metaKey) && e.key === 'u') {
            console.log('Content protection is active on this site.');
        }
    });

    // Prevent selection via shift+arrow keys on images
    document.addEventListener('keydown', function(e) {
        if (e.shiftKey && (e.key === 'ArrowUp' || e.key === 'ArrowDown' ||
                           e.key === 'ArrowLeft' || e.key === 'ArrowRight')) {
            var selection = window.getSelection();
            if (selection.anchorNode && selection.anchorNode.parentElement) {
                var img = selection.anchorNode.parentElement.querySelector('img');
                if (img && (img.closest('.wp-block-gallery') ||
                           img.closest('.wp-block-image') ||
                           img.closest('.site-content') ||
                           img.closest('.glightbox-container'))) {
                    e.preventDefault();
                }
            }
        }
    });

    // Additional protection for dynamically loaded lightbox content
    // Monitor DOM changes and apply protection to new lightbox elements
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 &&
                        (node.classList && node.classList.contains('glightbox-container'))) {
                        // Apply protection to newly added lightbox
                        var lightboxImages = node.querySelectorAll('img');
                        lightboxImages.forEach(function(img) {
                            img.addEventListener('contextmenu', function(e) {
                                e.preventDefault();
                                return false;
                            });
                            img.addEventListener('dragstart', function(e) {
                                e.preventDefault();
                                return false;
                            });
                        });
                    }
                });
            }
        });
    });

    // Start observing the document for lightbox additions
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Developer console message
    console.log('%cContent Protection Active', 'color: #c34143; font-weight: bold; font-size: 14px;');
    console.log('Images on this site are protected by copyright. Please respect the photographer\'s work.');

})();
