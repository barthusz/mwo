<?php
/**
 * Theme functions and definitions
 *
 * @package Mijn_Werk_Online
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Include required files
 */
require_once get_template_directory() . '/inc/social-media.php';
require_once get_template_directory() . '/inc/gallery-captions.php';

/**
 * Ensure gallery images have width and height attributes for proper space reservation
 * This prevents layout shift and helps Masonry calculate correct positions
 */
function mwo_add_dimensions_to_gallery_images( $attr, $attachment, $size ) {
    if ( ! isset( $attr['width'] ) || ! isset( $attr['height'] ) ) {
        $image = wp_get_attachment_image_src( $attachment->ID, $size );
        if ( $image ) {
            $attr['width'] = $image[1];
            $attr['height'] = $image[2];
        }
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'mwo_add_dimensions_to_gallery_images', 10, 3 );

/**
 * Theme setup
 */
function mwo_setup() {
    // Add theme support for various features
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'responsive-embeds' );

    // Register navigation menus
    register_nav_menus( array(
        'primary' => __( 'Primair Menu', 'mwo' ),
        'footer'  => __( 'Footer Menu', 'mwo' ),
    ) );
}
add_action( 'after_setup_theme', 'mwo_setup' );

/**
 * Enqueue scripts and styles
 */
function mwo_enqueue_assets() {
    // Intro screen styles and scripts (only on intro template)
    if ( is_page_template( 'template-intro.php' ) ) {
        wp_enqueue_style( 'mwo-intro-screen', get_template_directory_uri() . '/assets/css/intro-screen.css', array(), '1.0.1' );
        wp_enqueue_script( 'mwo-intro-screen', get_template_directory_uri() . '/js/intro-screen.js', array( 'jquery' ), '1.0.0', true );
        return; // Don't load other assets on intro screen
    }

    // Font Awesome (local)
    wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/all.min.css', array(), '6.5.1' );

    // Layout styles
    wp_enqueue_style( 'mwo-layout', get_template_directory_uri() . '/assets/css/layout.css', array(), '1.0.0' );

    // Sidebar styles
    wp_enqueue_style( 'mwo-sidebar', get_template_directory_uri() . '/assets/css/sidebar.css', array(), '1.0.0' );

    // Gallery styles
    wp_enqueue_style( 'mwo-gallery', get_template_directory_uri() . '/assets/css/gallery.css', array(), '2.1.0' );

    // Mobile menu styles
    wp_enqueue_style( 'mwo-mobile-menu', get_template_directory_uri() . '/assets/css/mobile-menu.css', array(), '1.0.0' );

    // GLightbox styles
    wp_enqueue_style( 'glightbox', get_template_directory_uri() . '/assets/css/glightbox.min.css', array(), '3.2.0' );
    wp_enqueue_style( 'mwo-lightbox-custom', get_template_directory_uri() . '/assets/css/lightbox-custom.css', array( 'glightbox' ), '1.0.0' );

    // Main theme styles
    wp_enqueue_style( 'mwo-style', get_stylesheet_uri(), array( 'font-awesome', 'mwo-layout', 'mwo-sidebar', 'mwo-gallery', 'glightbox' ), '1.0.0' );

    // Masonry (WordPress bundled)
    wp_enqueue_script( 'masonry' );

    // imagesLoaded (WordPress bundled)
    wp_enqueue_script( 'imagesloaded' );

    // Masonry initialization (uses window.load for better cached image handling)
    wp_enqueue_script( 'mwo-masonry-init', get_template_directory_uri() . '/js/masonry-init.js', array( 'jquery', 'masonry', 'imagesloaded' ), '2.0.0', true );

    // GLightbox
    wp_enqueue_script( 'glightbox', get_template_directory_uri() . '/js/glightbox.min.js', array(), '3.2.0', true );

    // Lightbox initialization
    wp_enqueue_script( 'mwo-lightbox-init', get_template_directory_uri() . '/js/lightbox-init.js', array( 'glightbox' ), '1.0.0', true );

    // Sticky header
    wp_enqueue_script( 'mwo-sticky-header', get_template_directory_uri() . '/js/sticky-header.js', array(), '1.0.0', true );

    // Mobile menu
    wp_enqueue_script( 'mwo-mobile-menu', get_template_directory_uri() . '/js/mobile-menu.js', array(), '1.0.0', true );

    // Pass theme options to JavaScript
    $options = get_option( 'mwo_options' );
    wp_localize_script( 'mwo-lightbox-init', 'mwoOptions', array(
        'lightboxCaptions' => isset( $options['lightbox_captions'] ) ? $options['lightbox_captions'] : 1,
    ) );
}
add_action( 'wp_enqueue_scripts', 'mwo_enqueue_assets' );

/**
 * Enqueue admin scripts
 */
function mwo_enqueue_admin_scripts( $hook ) {
    if ( 'dashboard_page_mwo-settings' !== $hook ) {
        return;
    }

    // Font Awesome for admin (local)
    wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/all.min.css', array(), '6.5.1' );

    wp_enqueue_media();
    wp_enqueue_script( 'mwo-admin', get_template_directory_uri() . '/js/admin.js', array( 'jquery' ), '1.0.1', true );
}
add_action( 'admin_enqueue_scripts', 'mwo_enqueue_admin_scripts' );

/**
 * Register widget areas
 */
function mwo_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'mwo' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Voeg widgets toe aan de sidebar.', 'mwo' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'mwo_widgets_init' );

/**
 * Add admin settings page
 */
function mwo_add_admin_page() {
    add_dashboard_page(
        __( 'Mijn Werk Online', 'mwo' ),
        __( 'Mijn Werk Online', 'mwo' ),
        'manage_options',
        'mwo-settings',
        'mwo_settings_page'
    );
}
add_action( 'admin_menu', 'mwo_add_admin_page' );

/**
 * Settings page content
 */
function mwo_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'mwo_settings' );
            do_settings_sections( 'mwo-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register settings
 */
function mwo_register_settings() {
    register_setting( 'mwo_settings', 'mwo_options', 'mwo_sanitize_options' );

    add_settings_section(
        'mwo_general_section',
        __( 'Algemene Instellingen', 'mwo' ),
        'mwo_general_section_callback',
        'mwo-settings'
    );

    add_settings_field(
        'mwo_menu_placement',
        __( 'Menu Plaatsing', 'mwo' ),
        'mwo_menu_placement_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_sticky_header',
        '<span id="mwo-sticky-header-label">' . __( 'Sticky Header', 'mwo' ) . '</span>',
        'mwo_sticky_header_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_logo',
        __( 'Logo', 'mwo' ),
        'mwo_logo_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_logo_width',
        __( 'Logo breedte', 'mwo' ),
        'mwo_logo_width_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_show_site_title',
        __( 'Sitetitel tonen', 'mwo' ),
        'mwo_show_site_title_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_show_tagline',
        __( 'Ondertitel tonen', 'mwo' ),
        'mwo_show_tagline_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_disable_page_titles',
        __( 'Paginakoppen uitschakelen', 'mwo' ),
        'mwo_disable_page_titles_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_disable_footer_credits',
        __( 'Footercredits uitschakelen', 'mwo' ),
        'mwo_disable_footer_credits_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_lightbox_captions',
        __( 'Lightbox bijschriften tonen', 'mwo' ),
        'mwo_lightbox_captions_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_enable_intro',
        '<span id="mwo-enable-intro-label">' . __( 'Intro scherm inschakelen', 'mwo' ) . '</span>',
        'mwo_enable_intro_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_intro_images',
        '<span id="mwo-intro-images-label">' . __( 'Intro achtergrondafbeeldingen', 'mwo' ) . '</span>',
        'mwo_intro_images_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_intro_button_text',
        '<span id="mwo-intro-button-text-label">' . __( 'Intro knoptekst', 'mwo' ) . '</span>',
        'mwo_intro_button_text_callback',
        'mwo-settings',
        'mwo_general_section'
    );
}
add_action( 'admin_init', 'mwo_register_settings' );

/**
 * Section callback
 */
function mwo_general_section_callback() {
    echo '<p>' . esc_html__( 'Configureer hier de instellingen voor Mijn Werk Online.', 'mwo' ) . '</p>';
}

/**
 * Menu placement field callback
 */
function mwo_menu_placement_callback() {
    $options = get_option( 'mwo_options' );
    $menu_placement = isset( $options['menu_placement'] ) ? $options['menu_placement'] : 'left';
    ?>
    <label>
        <input type="radio" name="mwo_options[menu_placement]" value="left" <?php checked( $menu_placement, 'left' ); ?>>
        <?php esc_html_e( 'Menu links', 'mwo' ); ?>
    </label>
    <br>
    <label>
        <input type="radio" name="mwo_options[menu_placement]" value="top" <?php checked( $menu_placement, 'top' ); ?>>
        <?php esc_html_e( 'Menu boven', 'mwo' ); ?>
    </label>
    <?php
}

/**
 * Sticky header field callback
 */
function mwo_sticky_header_callback() {
    $options = get_option( 'mwo_options' );
    $menu_placement = isset( $options['menu_placement'] ) ? $options['menu_placement'] : 'left';
    $sticky_header = isset( $options['sticky_header'] ) ? $options['sticky_header'] : 0;

    $style = $menu_placement === 'top' ? '' : 'style="display:none;"';
    ?>
    <div id="mwo-sticky-header-wrapper" <?php echo $style; ?>>
        <label>
            <input type="checkbox" name="mwo_options[sticky_header]" value="1" <?php checked( $sticky_header, 1 ); ?>>
            <?php esc_html_e( 'Sticky header inschakelen', 'mwo' ); ?>
        </label>
    </div>
    <script>
    jQuery(document).ready(function($) {
        function toggleStickyHeader() {
            var menuPlacement = $('input[name="mwo_options[menu_placement]"]:checked').val();
            if (menuPlacement === 'top') {
                $('#mwo-sticky-header-wrapper').show();
                $('#mwo-sticky-header-label').parent().parent().show();
            } else {
                $('#mwo-sticky-header-wrapper').hide();
                $('#mwo-sticky-header-label').parent().parent().hide();
            }
        }

        toggleStickyHeader();

        $('input[name="mwo_options[menu_placement]"]').on('change', function() {
            toggleStickyHeader();
        });
    });
    </script>
    <?php
}

/**
 * Logo upload field callback
 */
function mwo_logo_callback() {
    $options = get_option( 'mwo_options' );
    $logo_id = isset( $options['logo'] ) ? $options['logo'] : '';
    $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
    ?>
    <div class="mwo-logo-upload">
        <input type="hidden" name="mwo_options[logo]" id="mwo-logo-id" value="<?php echo esc_attr( $logo_id ); ?>">
        <div class="mwo-logo-preview" style="margin-bottom: 10px;">
            <?php if ( $logo_url ) : ?>
                <img src="<?php echo esc_url( $logo_url ); ?>" style="max-width: 200px; height: auto; display: block;">
            <?php endif; ?>
        </div>
        <button type="button" class="button mwo-upload-logo-button">
            <?php echo $logo_url ? esc_html__( 'Wijzig logo', 'mwo' ) : esc_html__( 'Upload logo', 'mwo' ); ?>
        </button>
        <?php if ( $logo_url ) : ?>
            <button type="button" class="button mwo-remove-logo-button"><?php esc_html_e( 'Verwijder logo', 'mwo' ); ?></button>
        <?php endif; ?>
        <p class="description"><?php esc_html_e( 'Upload een logo in hoge resolutie (retina) voor scherpe weergave.', 'mwo' ); ?></p>
    </div>
    <?php
}

/**
 * Logo width field callback
 */
function mwo_logo_width_callback() {
    $options = get_option( 'mwo_options' );
    $logo_width = isset( $options['logo_width'] ) ? $options['logo_width'] : 200;
    ?>
    <input type="number" name="mwo_options[logo_width]" value="<?php echo esc_attr( $logo_width ); ?>" min="50" max="800" step="1">
    <span>px</span>
    <p class="description"><?php esc_html_e( 'Maximale breedte van het logo (hoogte schaalt automatisch mee).', 'mwo' ); ?></p>
    <?php
}

/**
 * Show site title field callback
 */
function mwo_show_site_title_callback() {
    $options = get_option( 'mwo_options' );
    $show_site_title = isset( $options['show_site_title'] ) ? $options['show_site_title'] : 0;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[show_site_title]" value="1" <?php checked( $show_site_title, 1 ); ?>>
        <?php esc_html_e( 'Sitetitel weergeven in header', 'mwo' ); ?>
    </label>
    <?php
}

/**
 * Show tagline field callback
 */
function mwo_show_tagline_callback() {
    $options = get_option( 'mwo_options' );
    $show_tagline = isset( $options['show_tagline'] ) ? $options['show_tagline'] : 0;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[show_tagline]" value="1" <?php checked( $show_tagline, 1 ); ?>>
        <?php esc_html_e( 'Ondertitel weergeven in header', 'mwo' ); ?>
    </label>
    <?php
}

/**
 * Disable page titles field callback
 */
function mwo_disable_page_titles_callback() {
    $options = get_option( 'mwo_options' );
    $disable_page_titles = isset( $options['disable_page_titles'] ) ? $options['disable_page_titles'] : 0;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[disable_page_titles]" value="1" <?php checked( $disable_page_titles, 1 ); ?>>
        <?php esc_html_e( 'Verberg paginakoppen', 'mwo' ); ?>
    </label>
    <?php
}

/**
 * Disable footer credits field callback
 */
function mwo_disable_footer_credits_callback() {
    $options = get_option( 'mwo_options' );
    $disable_footer_credits = isset( $options['disable_footer_credits'] ) ? $options['disable_footer_credits'] : 0;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[disable_footer_credits]" value="1" <?php checked( $disable_footer_credits, 1 ); ?>>
        <?php esc_html_e( 'Verberg footer credits', 'mwo' ); ?>
    </label>
    <?php
}

/**
 * Lightbox captions field callback
 */
function mwo_lightbox_captions_callback() {
    $options = get_option( 'mwo_options' );
    $lightbox_captions = isset( $options['lightbox_captions'] ) ? $options['lightbox_captions'] : 1;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[lightbox_captions]" value="1" <?php checked( $lightbox_captions, 1 ); ?>>
        <?php esc_html_e( 'Toon bijschriften in lightbox (aan rechterkant)', 'mwo' ); ?>
    </label>
    <?php
}

/**
 * Enable intro screen field callback
 */
function mwo_enable_intro_callback() {
    $options = get_option( 'mwo_options' );
    $enable_intro = isset( $options['enable_intro'] ) ? $options['enable_intro'] : 0;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[enable_intro]" value="1" <?php checked( $enable_intro, 1 ); ?> id="mwo-enable-intro-checkbox">
        <?php esc_html_e( 'Toon intro scherm voordat bezoekers de site betreden', 'mwo' ); ?>
    </label>
    <script>
    jQuery(document).ready(function($) {
        function toggleIntroFields() {
            var isEnabled = $('#mwo-enable-intro-checkbox').is(':checked');
            if (isEnabled) {
                $('#mwo-intro-images-wrapper').show();
                $('#mwo-intro-button-text-wrapper').show();
                $('#mwo-intro-images-label').parent().parent().show();
                $('#mwo-intro-button-text-label').parent().parent().show();
            } else {
                $('#mwo-intro-images-wrapper').hide();
                $('#mwo-intro-button-text-wrapper').hide();
                $('#mwo-intro-images-label').parent().parent().hide();
                $('#mwo-intro-button-text-label').parent().parent().hide();
            }
        }

        toggleIntroFields();

        $('#mwo-enable-intro-checkbox').on('change', function() {
            toggleIntroFields();
        });
    });
    </script>
    <?php
}

/**
 * Intro images field callback
 */
function mwo_intro_images_callback() {
    $options = get_option( 'mwo_options' );
    $enable_intro = isset( $options['enable_intro'] ) ? $options['enable_intro'] : 0;
    $intro_images = isset( $options['intro_images'] ) ? $options['intro_images'] : array();

    $style = $enable_intro ? '' : 'style="display:none;"';
    ?>
    <div id="mwo-intro-images-wrapper" <?php echo $style; ?>>
        <div class="mwo-intro-images-list">
            <?php
            if ( ! empty( $intro_images ) && is_array( $intro_images ) ) {
                foreach ( $intro_images as $image_id ) {
                    $image_url = wp_get_attachment_image_url( $image_id, 'medium' );
                    if ( $image_url ) {
                        ?>
                        <div class="mwo-intro-image-item" style="display: inline-block; margin: 5px; position: relative;">
                            <img src="<?php echo esc_url( $image_url ); ?>" style="max-width: 150px; height: auto; display: block;">
                            <button type="button" class="button mwo-remove-intro-image" data-image-id="<?php echo esc_attr( $image_id ); ?>" style="position: absolute; top: 5px; right: 5px; padding: 2px 8px;">×</button>
                            <input type="hidden" name="mwo_options[intro_images][]" value="<?php echo esc_attr( $image_id ); ?>">
                        </div>
                        <?php
                    }
                }
            }
            ?>
        </div>
        <button type="button" class="button mwo-add-intro-image-button" style="margin-top: 10px;">
            <?php esc_html_e( 'Afbeelding toevoegen', 'mwo' ); ?>
        </button>
        <p class="description"><?php esc_html_e( 'Voeg één of meerdere afbeeldingen toe. Bij meerdere afbeeldingen worden ze als slideshow getoond.', 'mwo' ); ?></p>
    </div>
    <?php
}

/**
 * Intro button text field callback
 */
function mwo_intro_button_text_callback() {
    $options = get_option( 'mwo_options' );
    $enable_intro = isset( $options['enable_intro'] ) ? $options['enable_intro'] : 0;
    $intro_button_text = isset( $options['intro_button_text'] ) ? $options['intro_button_text'] : 'VIEW MY WORK';

    $style = $enable_intro ? '' : 'style="display:none;"';
    ?>
    <div id="mwo-intro-button-text-wrapper" <?php echo $style; ?>>
        <input type="text" name="mwo_options[intro_button_text]" value="<?php echo esc_attr( $intro_button_text ); ?>" class="regular-text">
        <p class="description"><?php esc_html_e( 'De tekst die op de knop wordt weergegeven.', 'mwo' ); ?></p>
    </div>
    <?php
}

/**
 * Redirect to intro screen if enabled
 */
function mwo_maybe_redirect_to_intro() {
    // Don't redirect in admin, on login pages, or if skip parameter is set
    if ( is_admin() || isset( $_GET['skip_intro'] ) || is_404() ) {
        return;
    }

    // Check if we're already on the intro page
    if ( is_page_template( 'template-intro.php' ) ) {
        return;
    }

    // Check if intro screen is enabled
    $options = get_option( 'mwo_options' );
    $enable_intro = isset( $options['enable_intro'] ) && $options['enable_intro'];

    if ( ! $enable_intro ) {
        return;
    }

    // Check if intro page exists
    $intro_page = get_pages( array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'template-intro.php',
        'number'     => 1,
    ) );

    if ( empty( $intro_page ) ) {
        return;
    }

    // Start session if not already started
    if ( ! isset( $_SESSION ) ) {
        session_start();
    }

    // Force show intro with ?show_intro=1 parameter (for testing/preview)
    if ( isset( $_GET['show_intro'] ) ) {
        wp_redirect( get_permalink( $intro_page[0]->ID ) );
        exit;
    }

    // Reset session with ?reset_intro=1 parameter (for testing)
    if ( isset( $_GET['reset_intro'] ) ) {
        unset( $_SESSION['mwo_intro_seen'] );
        wp_redirect( home_url( '/' ) );
        exit;
    }

    // Only redirect on homepage
    if ( is_front_page() && ! isset( $_GET['skip_intro'] ) ) {
        if ( ! isset( $_SESSION['mwo_intro_seen'] ) ) {
            $_SESSION['mwo_intro_seen'] = true;
            wp_redirect( get_permalink( $intro_page[0]->ID ) );
            exit;
        }
    }
}
add_action( 'template_redirect', 'mwo_maybe_redirect_to_intro' );

/**
 * Sanitize options
 */
function mwo_sanitize_options( $input ) {
    $existing = get_option( 'mwo_options', array() );
    $sanitized = array();

    // Menu placement
    if ( isset( $input['menu_placement'] ) ) {
        $sanitized['menu_placement'] = in_array( $input['menu_placement'], array( 'left', 'top' ) ) ? $input['menu_placement'] : 'left';
    } else {
        $sanitized['menu_placement'] = isset( $existing['menu_placement'] ) ? $existing['menu_placement'] : 'left';
    }

    // Logo
    if ( isset( $input['logo'] ) ) {
        $sanitized['logo'] = absint( $input['logo'] );
    }

    // Logo width
    if ( isset( $input['logo_width'] ) ) {
        $sanitized['logo_width'] = absint( $input['logo_width'] );
        if ( $sanitized['logo_width'] < 50 ) {
            $sanitized['logo_width'] = 50;
        } elseif ( $sanitized['logo_width'] > 800 ) {
            $sanitized['logo_width'] = 800;
        }
    }

    // Checkboxes
    $sanitized['sticky_header'] = isset( $input['sticky_header'] ) ? 1 : 0;
    $sanitized['show_site_title'] = isset( $input['show_site_title'] ) ? 1 : 0;
    $sanitized['show_tagline'] = isset( $input['show_tagline'] ) ? 1 : 0;
    $sanitized['disable_page_titles'] = isset( $input['disable_page_titles'] ) ? 1 : 0;
    $sanitized['disable_footer_credits'] = isset( $input['disable_footer_credits'] ) ? 1 : 0;
    $sanitized['lightbox_captions'] = isset( $input['lightbox_captions'] ) ? 1 : 0;
    $sanitized['enable_intro'] = isset( $input['enable_intro'] ) ? 1 : 0;

    // Intro images
    if ( isset( $input['intro_images'] ) && is_array( $input['intro_images'] ) ) {
        $sanitized['intro_images'] = array_map( 'absint', $input['intro_images'] );
    } else {
        $sanitized['intro_images'] = array();
    }

    // Intro button text
    if ( isset( $input['intro_button_text'] ) ) {
        $sanitized['intro_button_text'] = sanitize_text_field( $input['intro_button_text'] );
    } else {
        $sanitized['intro_button_text'] = 'VIEW MY WORK';
    }

    // Social media URLs
    $sanitized = mwo_sanitize_social_urls( $input, $sanitized );

    return $sanitized;
}
