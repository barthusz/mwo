<?php
/**
 * Footer template
 *
 * @package Mijn_Werk_Online
 */
?>

<footer class="site-footer">
    <div class="footer-content">
        <p>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Alle rechten voorbehouden.', 'mwo' ); ?></p>

        <?php
        wp_nav_menu( array(
            'theme_location' => 'footer',
            'menu_id'        => 'footer-menu',
            'fallback_cb'    => false,
        ) );
        ?>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
