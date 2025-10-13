<?php
/**
 * Footer template
 *
 * @package Mijn_Werk_Online
 */

// Get theme options
$options = get_option( 'mwo_options' );
$disable_footer_credits = isset( $options['disable_footer_credits'] ) && $options['disable_footer_credits'] ? true : false;
?>

</div><!-- .site-main-wrapper -->

<?php if ( ! $disable_footer_credits ) : ?>
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
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
