/**
 * Sticky Header - Make navigation sticky after scrolling past the logo
 */
(function() {
    'use strict';

    // Only run for top menu with sticky header enabled
    if (!document.body.classList.contains('menu-top') || !document.body.classList.contains('sticky-header')) {
        return;
    }

    const branding = document.querySelector('.site-branding');
    const navigation = document.querySelector('.site-navigation');

    if (!branding || !navigation) {
        return;
    }

    // Get the offset position of the navigation
    const navOffset = branding.offsetHeight;

    function handleScroll() {
        if (window.pageYOffset >= navOffset) {
            navigation.classList.add('is-sticky');
        } else {
            navigation.classList.remove('is-sticky');
        }
    }

    // Listen for scroll events
    window.addEventListener('scroll', handleScroll);

    // Check on load
    handleScroll();
})();
