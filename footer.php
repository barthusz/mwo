<?php
/**
 * Footer template
 *
 * @package Mijn_Werk_Online
 */

// Get theme options
$options = get_option( 'mwo_options' );
$menu_placement = isset( $options['menu_placement'] ) ? $options['menu_placement'] : 'left';
?>

</div><!-- .site-main-wrapper -->

<?php if ( $menu_placement === 'top' ) : ?>
<footer class="site-footer">
    <div class="footer-content">
        <?php
        // Social media icons
        $social_links = isset( $options['social'] ) ? $options['social'] : array();
        if ( ! empty( $social_links ) ) {
            $platforms = mwo_get_social_platforms();
            ?>
            <div class="social-media-links">
                <?php
                foreach ( $social_links as $platform => $url ) {
                    if ( ! empty( $url ) && isset( $platforms[ $platform ] ) ) {
                        printf(
                            '<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s"><i class="%s"></i></a>',
                            esc_url( $url ),
                            esc_attr( $platforms[ $platform ]['label'] ),
                            esc_attr( $platforms[ $platform ]['icon'] )
                        );
                    }
                }
                ?>
            </div>
            <?php
        }
        ?>

        <p class="copyright">&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?> | Powered by <a href="https://www.mijnwerkonline.nl/" target="_blank" title="Wil je ook een site als deze?">Mijn Werk Online</a></p>

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
