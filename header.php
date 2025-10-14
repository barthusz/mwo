<?php
/**
 * Header template
 *
 * @package Mijn_Werk_Online
 */

// Get theme options
$options = get_option( 'mwo_options' );
$menu_placement = isset( $options['menu_placement'] ) ? $options['menu_placement'] : 'left';
$sticky_header = isset( $options['sticky_header'] ) && $options['sticky_header'] ? true : false;
$show_site_title = isset( $options['show_site_title'] ) && $options['show_site_title'] ? true : false;
$show_tagline = isset( $options['show_tagline'] ) && $options['show_tagline'] ? true : false;
$logo_id = isset( $options['logo'] ) ? $options['logo'] : '';
$logo_width = isset( $options['logo_width'] ) ? $options['logo_width'] : 200;
$enable_intro = isset( $options['enable_intro'] ) && $options['enable_intro'] ? true : false;

// Determine logo link URL
$logo_link_url = home_url( '/' );
if ( $enable_intro ) {
    $intro_page = get_pages( array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'template-intro.php',
        'number'     => 1,
    ) );
    if ( ! empty( $intro_page ) ) {
        $logo_link_url = get_permalink( $intro_page[0]->ID );
    }
}

// Build body classes
$body_classes = array();
$body_classes[] = 'menu-' . $menu_placement;
if ( $menu_placement === 'top' && $sticky_header ) {
    $body_classes[] = 'sticky-header';
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class( $body_classes ); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="mobile-header">
        <div class="site-branding-mobile">
            <?php
            // Logo for mobile
            if ( $logo_id ) {
                $logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
                if ( $logo_url ) {
                    printf(
                        '<a href="%s" class="custom-logo-link" rel="home"><img src="%s" alt="%s" class="custom-logo" style="max-width: %dpx; height: auto;"></a>',
                        esc_url( $logo_link_url ),
                        esc_url( $logo_url ),
                        esc_attr( get_bloginfo( 'name' ) ),
                        absint( $logo_width )
                    );
                }
            }
            ?>
        </div>
        <button class="mobile-menu-toggle" aria-label="Menu" aria-expanded="false">
            <span class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </button>
    </div>

    <div class="site-branding">
        <?php
        // Logo
        if ( $logo_id ) {
            $logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
            if ( $logo_url ) {
                printf(
                    '<a href="%s" class="custom-logo-link" rel="home"><img src="%s" alt="%s" class="custom-logo" style="max-width: %dpx; height: auto;"></a>',
                    esc_url( $logo_link_url ),
                    esc_url( $logo_url ),
                    esc_attr( get_bloginfo( 'name' ) ),
                    absint( $logo_width )
                );
            }
        }

        // Site title
        if ( $show_site_title ) {
            printf(
                '<h1 class="site-title"><a href="%s" rel="home">%s</a></h1>',
                esc_url( $logo_link_url ),
                esc_html( get_bloginfo( 'name' ) )
            );
        }

        // Tagline
        if ( $show_tagline ) {
            $description = get_bloginfo( 'description', 'display' );
            if ( $description ) {
                printf( '<p class="site-description">%s</p>', esc_html( $description ) );
            }
        }
        ?>
    </div>

    <nav class="site-navigation">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'menu_id'        => 'primary-menu',
            'fallback_cb'    => false,
        ) );
        ?>
    </nav>

    <?php
    // Social media icons - only show in left menu
    if ( $menu_placement === 'left' ) {
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
    }
    ?>
</header>

<div class="site-main-wrapper">
