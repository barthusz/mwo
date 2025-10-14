/**
 * Mobile Menu Toggle
 */
(function() {
    'use strict';

    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const navigation = document.querySelector('.site-navigation');
    const body = document.body;

    if (!menuToggle || !navigation) {
        return;
    }

    menuToggle.addEventListener('click', function() {
        const isActive = menuToggle.classList.contains('active');

        if (isActive) {
            // Close menu
            menuToggle.classList.remove('active');
            navigation.classList.remove('active');
            menuToggle.setAttribute('aria-expanded', 'false');
            body.style.overflow = '';
        } else {
            // Open menu
            menuToggle.classList.add('active');
            navigation.classList.add('active');
            menuToggle.setAttribute('aria-expanded', 'true');
            body.style.overflow = 'hidden';
        }
    });

    // Close menu when clicking on a link
    const menuLinks = navigation.querySelectorAll('a');
    menuLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            menuToggle.classList.remove('active');
            navigation.classList.remove('active');
            menuToggle.setAttribute('aria-expanded', 'false');
            body.style.overflow = '';
        });
    });

    // Close menu on window resize if viewport gets wider
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                menuToggle.classList.remove('active');
                navigation.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
            }
        }, 250);
    });
})();
