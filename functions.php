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
require_once get_template_directory() . '/inc/admin-settings.php';

/**
 * Disable unnecessary WordPress image sizes
 * This prevents WordPress from generating extra image sizes that aren't needed
 * Only runs if the option is enabled in settings
 */
function mwo_disable_extra_image_sizes( $sizes ) {
    $options = get_option( 'mwo_options' );
    $disable_extra_sizes = isset( $options['disable_extra_sizes'] ) ? $options['disable_extra_sizes'] : 1;

    // Only disable if setting is enabled (default: on)
    if ( ! $disable_extra_sizes ) {
        return $sizes;
    }

    // Remove medium_large (768px)
    unset( $sizes['medium_large'] );

    // Remove 2x sizes for large (1536x1536 and 2048x2048)
    unset( $sizes['1536x1536'] );
    unset( $sizes['2048x2048'] );

    return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'mwo_disable_extra_image_sizes' );

/**
 * Disable big image threshold
 * WordPress 5.3+ automatically scales down images larger than 2560px
 * We handle this ourselves with the auto-resize feature
 * Only runs if extra sizes are disabled
 */
function mwo_disable_big_image_threshold() {
    $options = get_option( 'mwo_options' );
    $disable_extra_sizes = isset( $options['disable_extra_sizes'] ) ? $options['disable_extra_sizes'] : 1;

    // Only disable threshold if extra sizes are disabled
    if ( ! $disable_extra_sizes ) {
        return 2560; // Keep WordPress default
    }

    return false; // Disable the 2560px threshold completely
}
add_filter( 'big_image_size_threshold', 'mwo_disable_big_image_threshold' );

/**
 * Optimize srcset for galleries - exclude full size original
 * This improves performance by only serving appropriate thumbnail sizes
 * Full size is still available for lightbox and direct viewing
 */
function mwo_optimize_gallery_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
    $options = get_option( 'mwo_options' );
    $optimize_srcset = isset( $options['optimize_srcset'] ) ? $options['optimize_srcset'] : 1;

    // Only optimize if setting is enabled (default: on)
    if ( ! $optimize_srcset ) {
        return $sources;
    }

    // Get the max size we want in srcset (large = 1024px by default)
    $max_srcset_width = get_option( 'large_size_w', 1024 );

    // Remove any sources larger than our max width
    foreach ( $sources as $width => $source ) {
        if ( $width > $max_srcset_width ) {
            unset( $sources[ $width ] );
        }
    }

    return $sources;
}
add_filter( 'wp_calculate_image_srcset', 'mwo_optimize_gallery_srcset', 10, 5 );

/**
 * Automatically resize large images on upload
 * This reduces file size and improves page load times
 * Uses wp_handle_upload filter which runs after the file is moved to uploads directory
 */
function mwo_auto_resize_uploaded_images( $upload ) {
    $options = get_option( 'mwo_options' );
    $auto_resize = isset( $options['auto_resize_images'] ) && $options['auto_resize_images'];

    // Only proceed if auto-resize is enabled
    if ( ! $auto_resize ) {
        return $upload;
    }

    $max_size = isset( $options['max_image_size'] ) ? absint( $options['max_image_size'] ) : 2400;

    // Check if upload was successful and is an image
    if ( ! isset( $upload['file'] ) || ! isset( $upload['type'] ) ) {
        return $upload;
    }

    if ( strpos( $upload['type'], 'image' ) === false ) {
        return $upload;
    }

    $file_path = $upload['file'];

    // Get image dimensions
    $image_size = @getimagesize( $file_path );
    if ( ! $image_size ) {
        return $upload;
    }

    list( $width, $height ) = $image_size;

    // Check if resize is needed (if either dimension exceeds max)
    if ( $width <= $max_size && $height <= $max_size ) {
        return $upload;
    }

    // Load WordPress image editor
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    $image_editor = wp_get_image_editor( $file_path );

    if ( is_wp_error( $image_editor ) ) {
        return $upload;
    }

    // Calculate new dimensions (maintain aspect ratio)
    if ( $width > $height ) {
        $new_width = $max_size;
        $new_height = intval( $height * ( $max_size / $width ) );
    } else {
        $new_height = $max_size;
        $new_width = intval( $width * ( $max_size / $height ) );
    }

    // Resize the image
    $resize_result = $image_editor->resize( $new_width, $new_height, false );

    if ( is_wp_error( $resize_result ) ) {
        return $upload;
    }

    // Save the resized image (overwrite original)
    $saved = $image_editor->save( $file_path );

    if ( is_wp_error( $saved ) ) {
        return $upload;
    }

    // Update file size in upload array
    $upload['file'] = $saved['path'];
    if ( file_exists( $saved['path'] ) ) {
        $upload['size'] = filesize( $saved['path'] );
    }

    return $upload;
}
add_filter( 'wp_handle_upload', 'mwo_auto_resize_uploaded_images' );

/**
 * Ensure gallery images have width and height attributes for proper space reservation
 * This prevents layout shift and helps Masonry calculate correct positions
 * Only needed when Masonry is enabled
 */
function mwo_add_dimensions_to_gallery_images( $attr, $attachment, $size ) {
    $options = get_option( 'mwo_options' );
    $enable_masonry = isset( $options['enable_masonry'] ) ? $options['enable_masonry'] : 1;

    // Only add dimensions when Masonry is enabled
    if ( ! $enable_masonry ) {
        return $attr;
    }

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

    // Get theme options for dynamic CSS
    $options = get_option( 'mwo_options' );
    $content_container_width = isset( $options['content_container_width'] ) ? $options['content_container_width'] : 1170;
    $enable_masonry = isset( $options['enable_masonry'] ) ? $options['enable_masonry'] : 1;
    $content_protection = isset( $options['content_protection'] ) ? $options['content_protection'] : 0;

    // Font Awesome (local)
    wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/all.min.css', array(), '6.5.1' );

    // Layout styles
    wp_enqueue_style( 'mwo-layout', get_template_directory_uri() . '/assets/css/layout.css', array(), '1.1.3' );

    // Sidebar styles
    wp_enqueue_style( 'mwo-sidebar', get_template_directory_uri() . '/assets/css/sidebar.css', array(), '1.0.0' );

    // Gallery styles (only load when masonry is enabled)
    if ( $enable_masonry ) {
        wp_enqueue_style( 'mwo-gallery', get_template_directory_uri() . '/assets/css/gallery.css', array(), '2.1.0' );
    }

    // Mobile menu styles
    wp_enqueue_style( 'mwo-mobile-menu', get_template_directory_uri() . '/assets/css/mobile-menu.css', array(), '1.0.0' );

    // GLightbox styles
    wp_enqueue_style( 'glightbox', get_template_directory_uri() . '/assets/css/glightbox.min.css', array(), '3.2.0' );
    wp_enqueue_style( 'mwo-lightbox-custom', get_template_directory_uri() . '/assets/css/lightbox-custom.css', array( 'glightbox' ), '1.3.0' );

    // Gutenberg block styles
    wp_enqueue_style( 'mwo-guten', get_template_directory_uri() . '/assets/css/guten.css', array(), '1.0.0' );

    // Other custom styles
    wp_enqueue_style( 'mwo-other', get_template_directory_uri() . '/assets/css/other.css', array(), '1.0.0' );

    // Main theme styles
    wp_enqueue_style( 'mwo-style', get_stylesheet_uri(), array( 'font-awesome', 'mwo-layout', 'mwo-sidebar', 'mwo-gallery', 'glightbox' ), '1.0.0' );

    // Masonry (conditionally loaded based on setting)
    if ( $enable_masonry ) {
        wp_enqueue_script( 'masonry' );
        wp_enqueue_script( 'imagesloaded' );
        wp_enqueue_script( 'mwo-masonry-init', get_template_directory_uri() . '/js/masonry-init.js', array( 'jquery', 'masonry', 'imagesloaded' ), '2.0.0', true );
    }

    // GLightbox
    wp_enqueue_script( 'glightbox', get_template_directory_uri() . '/js/glightbox.min.js', array(), '3.2.0', true );

    // Lightbox initialization
    wp_enqueue_script( 'mwo-lightbox-init', get_template_directory_uri() . '/js/lightbox-init.js', array( 'glightbox' ), '1.6.0', true );

    // Sticky header
    wp_enqueue_script( 'mwo-sticky-header', get_template_directory_uri() . '/js/sticky-header.js', array(), '1.0.0', true );

    // Mobile menu
    wp_enqueue_script( 'mwo-mobile-menu', get_template_directory_uri() . '/js/mobile-menu.js', array(), '1.0.0', true );

    // Content protection (conditionally loaded)
    if ( $content_protection ) {
        wp_enqueue_script( 'mwo-content-protection', get_template_directory_uri() . '/js/content-protection.js', array(), '1.0.0', true );
    }

    // Pass theme options to JavaScript
    wp_localize_script( 'mwo-lightbox-init', 'mwoOptions', array(
        'lightboxCaptions' => isset( $options['lightbox_captions'] ) ? $options['lightbox_captions'] : 1,
    ) );

    // Add inline CSS for dynamic settings
    $menu_accent_color = isset( $options['menu_accent_color'] ) ? $options['menu_accent_color'] : '#c34143';
    $link_color = isset( $options['link_color'] ) ? $options['link_color'] : '#c34143';

    $custom_css = "
        .content-container {
            max-width: {$content_container_width}px;
        }
        .site-navigation a:hover,
        .site-navigation .current-menu-item > a,
        body.darkmode .site-navigation a:hover,
        body.darkmode .site-navigation .current-menu-item > a {
            color: {$menu_accent_color} !important;
        }
        .social-media-links a:hover,
        body.darkmode .social-media-links a:hover {
            color: {$menu_accent_color} !important;
        }
        .site-footer .social-media-links a:hover,
        body.darkmode .site-footer .social-media-links a:hover {
            color: {$menu_accent_color} !important;
        }
        .site-content a,
        .entry-content a,
        body.darkmode .site-content a,
        body.darkmode .entry-content a {
            color: {$link_color};
        }
    ";

    wp_add_inline_style( 'mwo-layout', $custom_css );
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

    // WordPress color picker
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );

    wp_enqueue_media();
    wp_enqueue_script( 'mwo-admin', get_template_directory_uri() . '/js/admin.js', array( 'jquery', 'wp-color-picker' ), '1.0.2', true );
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
    // Get theme version dynamically
    $theme = wp_get_theme();
    $version = $theme->get( 'Version' );
    ?>
    <div class="wrap">
        <h1>
            <?php echo esc_html( get_admin_page_title() ); ?>
            <span style="font-size: 14px; font-weight: normal; color: #666; margin-left: 10px;">v<?php echo esc_html( $version ); ?></span>
        </h1>
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
 * NOTE: This function is now replaced by inc/admin-settings.php for better organization
 * Kept here for backwards compatibility with callback functions
 */
function mwo_register_settings_OLD() {
    // This function is now handled by inc/admin-settings.php
    return;

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
        'mwo_menu_accent_color',
        __( 'Menu accent kleur', 'mwo' ),
        'mwo_menu_accent_color_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_link_color',
        __( 'Link kleur', 'mwo' ),
        'mwo_link_color_callback',
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

    add_settings_field(
        'mwo_content_container_width',
        __( 'Content Container breedte', 'mwo' ),
        'mwo_content_container_width_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_enable_masonry',
        __( 'Masonry layout voor galerijen', 'mwo' ),
        'mwo_enable_masonry_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_content_protection',
        __( 'Content protectie', 'mwo' ),
        'mwo_content_protection_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_auto_resize_images',
        '<span id="mwo-auto-resize-label">' . __( 'Automatisch foto\'s verkleinen', 'mwo' ) . '</span>',
        'mwo_auto_resize_images_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_max_image_size',
        '<span id="mwo-max-image-size-label">' . __( 'Maximale foto grootte', 'mwo' ) . '</span>',
        'mwo_max_image_size_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_disable_extra_sizes',
        __( 'Extra afbeeldingsformaten', 'mwo' ),
        'mwo_disable_extra_sizes_callback',
        'mwo-settings',
        'mwo_general_section'
    );

    add_settings_field(
        'mwo_optimize_srcset',
        __( 'Gallery performance optimalisatie', 'mwo' ),
        'mwo_optimize_srcset_callback',
        'mwo-settings',
        'mwo_general_section'
    );
}
// Oude add_action uitgeschakeld - nu geregeld in inc/admin-settings.php
// add_action( 'admin_init', 'mwo_register_settings' );

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
 * Menu accent color field callback
 */
function mwo_menu_accent_color_callback() {
    $options = get_option( 'mwo_options' );
    $menu_accent_color = isset( $options['menu_accent_color'] ) ? $options['menu_accent_color'] : '#c34143';
    ?>
    <input type="text" name="mwo_options[menu_accent_color]" value="<?php echo esc_attr( $menu_accent_color ); ?>" class="mwo-color-picker">
    <p class="description"><?php esc_html_e( 'Kleur voor actieve menu items, hover effecten en social media iconen.', 'mwo' ); ?></p>
    <?php
}

/**
 * Link color field callback
 */
function mwo_link_color_callback() {
    $options = get_option( 'mwo_options' );
    $link_color = isset( $options['link_color'] ) ? $options['link_color'] : '#c34143';
    ?>
    <input type="text" name="mwo_options[link_color]" value="<?php echo esc_attr( $link_color ); ?>" class="mwo-color-picker">
    <p class="description"><?php esc_html_e( 'Kleur voor links in de content.', 'mwo' ); ?></p>
    <?php
}

/**
 * Darkmode field callback
 */
function mwo_darkmode_callback() {
    $options = get_option( 'mwo_options' );
    $darkmode = isset( $options['darkmode'] ) ? $options['darkmode'] : 0;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[darkmode]" value="1" <?php checked( $darkmode, 1 ); ?>>
        <?php esc_html_e( 'Darkmode inschakelen (zwarte achtergrond, witte tekst)', 'mwo' ); ?>
    </label>
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
 * Content container width field callback
 */
function mwo_content_container_width_callback() {
    $options = get_option( 'mwo_options' );
    $content_container_width = isset( $options['content_container_width'] ) ? $options['content_container_width'] : 1170;
    ?>
    <input type="number" name="mwo_options[content_container_width]" value="<?php echo esc_attr( $content_container_width ); ?>" min="400" max="2000" step="10">
    <span>px</span>
    <p class="description"><?php esc_html_e( 'Maximale breedte van de content container in de "Content Container" template.', 'mwo' ); ?></p>
    <?php
}

/**
 * Enable masonry field callback
 */
function mwo_enable_masonry_callback() {
    $options = get_option( 'mwo_options' );
    $enable_masonry = isset( $options['enable_masonry'] ) ? $options['enable_masonry'] : 1;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[enable_masonry]" value="1" <?php checked( $enable_masonry, 1 ); ?>>
        <?php esc_html_e( 'Masonry layout inschakelen voor galerijen (aanbevolen)', 'mwo' ); ?>
    </label>
    <p class="description"><?php esc_html_e( 'Uitschakelen zorgt voor een standaard grid layout zonder JavaScript.', 'mwo' ); ?></p>
    <?php
}

/**
 * Content protection field callback
 */
function mwo_content_protection_callback() {
    $options = get_option( 'mwo_options' );
    $content_protection = isset( $options['content_protection'] ) ? $options['content_protection'] : 0;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[content_protection]" value="1" <?php checked( $content_protection, 1 ); ?>>
        <?php esc_html_e( 'Content protectie inschakelen', 'mwo' ); ?>
    </label>
    <p class="description"><?php esc_html_e( 'Bescherm foto\'s tegen downloaden via rechtermuisklik, slepen en kopiëren. Let op: dit biedt geen 100% bescherming tegen technisch bekwame gebruikers.', 'mwo' ); ?></p>
    <?php
}

/**
 * Auto resize images field callback
 */
function mwo_auto_resize_images_callback() {
    $options = get_option( 'mwo_options' );
    $auto_resize = isset( $options['auto_resize_images'] ) ? $options['auto_resize_images'] : 0;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[auto_resize_images]" value="1" <?php checked( $auto_resize, 1 ); ?> id="mwo-auto-resize-checkbox">
        <?php esc_html_e( 'Verkleinen van foto\'s bij upload inschakelen', 'mwo' ); ?>
    </label>
    <p class="description"><?php esc_html_e( 'Grote foto\'s worden automatisch verkleind bij upload. Dit bespaart ruimte en verbetert de laadsnelheid.', 'mwo' ); ?></p>
    <script>
    jQuery(document).ready(function($) {
        function toggleMaxImageSize() {
            var isEnabled = $('#mwo-auto-resize-checkbox').is(':checked');
            if (isEnabled) {
                $('#mwo-max-image-size-wrapper').show();
                $('#mwo-max-image-size-label').parent().parent().show();
            } else {
                $('#mwo-max-image-size-wrapper').hide();
                $('#mwo-max-image-size-label').parent().parent().hide();
            }
        }

        toggleMaxImageSize();

        $('#mwo-auto-resize-checkbox').on('change', function() {
            toggleMaxImageSize();
        });
    });
    </script>
    <?php
}

/**
 * Max image size field callback
 */
function mwo_max_image_size_callback() {
    $options = get_option( 'mwo_options' );
    $auto_resize = isset( $options['auto_resize_images'] ) ? $options['auto_resize_images'] : 0;
    $max_image_size = isset( $options['max_image_size'] ) ? $options['max_image_size'] : 2400;

    $style = $auto_resize ? '' : 'style="display:none;"';
    ?>
    <div id="mwo-max-image-size-wrapper" <?php echo $style; ?>>
        <input type="number" name="mwo_options[max_image_size]" value="<?php echo esc_attr( $max_image_size ); ?>" min="800" max="5000" step="100">
        <span>px</span>
        <p class="description"><?php esc_html_e( 'Maximale breedte/hoogte voor geüploade foto\'s. Foto\'s groter dan deze waarde worden automatisch verkleind (langste zijde).', 'mwo' ); ?></p>
    </div>
    <?php
}

/**
 * Disable extra image sizes field callback
 */
function mwo_disable_extra_sizes_callback() {
    $options = get_option( 'mwo_options' );
    $disable_extra_sizes = isset( $options['disable_extra_sizes'] ) ? $options['disable_extra_sizes'] : 1;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[disable_extra_sizes]" value="1" <?php checked( $disable_extra_sizes, 1 ); ?>>
        <?php esc_html_e( 'Schakel onnodige WordPress afbeeldingsformaten uit', 'mwo' ); ?>
    </label>
    <p class="description">
        <?php esc_html_e( 'WordPress genereert standaard extra formaten (medium_large 768px, 1536x1536, 2048x2048). Aanvinken schakelt deze uit en bespaart schijfruimte.', 'mwo' ); ?>
        <br>
        <strong><?php esc_html_e( 'Aanbevolen: Aangevinkt laten', 'mwo' ); ?></strong>
        <?php esc_html_e( '(je Media instellingen blijven werken: thumbnail, medium, large)', 'mwo' ); ?>
    </p>
    <?php
}

/**
 * Optimize srcset field callback
 */
function mwo_optimize_srcset_callback() {
    $options = get_option( 'mwo_options' );
    $optimize_srcset = isset( $options['optimize_srcset'] ) ? $options['optimize_srcset'] : 1;
    ?>
    <label>
        <input type="checkbox" name="mwo_options[optimize_srcset]" value="1" <?php checked( $optimize_srcset, 1 ); ?>>
        <?php esc_html_e( 'Beperk gallery afbeeldingen tot max 1024px', 'mwo' ); ?>
    </label>
    <p class="description">
        <?php esc_html_e( 'Voorkomt dat het volledige origineel (2400px) in galleries wordt geladen. Galleries gebruiken dan alleen thumbnail, medium en large formaten.', 'mwo' ); ?>
        <br>
        <strong><?php esc_html_e( 'Aanbevolen: Aangevinkt laten', 'mwo' ); ?></strong>
        <?php esc_html_e( '(Lightbox toont nog steeds het origineel in volle kwaliteit)', 'mwo' ); ?>
        <br>
        <em style="color: #666;"><?php esc_html_e( 'Verbetert laadsnelheid aanzienlijk bij galleries met veel foto\'s.', 'mwo' ); ?></em>
    </p>
    <?php
}

/**
 * Configure cache headers for intro screen functionality (works with all cache plugins)
 *
 * This function ensures the intro screen works correctly with ALL caching solutions:
 * - LiteSpeed Cache
 * - WP Rocket
 * - W3 Total Cache
 * - WP Super Cache
 * - Cloudflare
 * - Any other WordPress cache plugin
 *
 * The intro page uses DONOTCACHEPAGE constant which is respected by all major cache plugins.
 * The homepage uses JavaScript-based redirect which works with cached pages.
 */
function mwo_cache_config_for_intro() {
    // Check if intro screen is enabled
    $options = get_option( 'mwo_options' );
    $enable_intro = isset( $options['enable_intro'] ) && $options['enable_intro'];

    if ( ! $enable_intro ) {
        return;
    }

    // Don't cache the intro template page (universal headers)
    if ( is_page_template( 'template-intro.php' ) ) {
        if ( ! headers_sent() ) {
            // LiteSpeed Cache
            header( 'X-LiteSpeed-Cache-Control: no-cache' );
            // Standard cache headers (WP Rocket, W3 Total Cache, etc.)
            header( 'Cache-Control: no-cache, no-store, must-revalidate, max-age=0' );
            header( 'Pragma: no-cache' );
            header( 'Expires: 0' );
        }

        // Exclude from cache plugins
        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( 'DONOTCACHEPAGE', true );
        }
    }
}
add_action( 'template_redirect', 'mwo_cache_config_for_intro', 1 );

/**
 * Add intro redirect script to homepage (cache-friendly with JavaScript)
 */
function mwo_intro_redirect_script() {
    // Don't run in admin
    if ( is_admin() ) {
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

    $intro_url = get_permalink( $intro_page[0]->ID );

    // Only add script on homepage (front page or home URL without path)
    $is_homepage = is_front_page() || ( is_home() && ! is_paged() );

    if ( $is_homepage ) {
        // Add inline JavaScript to check cookie and redirect (works with cache)
        ?>
        <script>
        (function() {
            // Check if intro has been seen or if skip parameter is present
            var allCookies = document.cookie;
            var hasSeenIntro = allCookies.indexOf('mwo_intro_seen') !== -1;
            var urlParams = new URLSearchParams(window.location.search);
            var skipIntro = urlParams.has('skip_intro');
            var showIntro = urlParams.has('show_intro');
            var resetIntro = urlParams.has('reset_intro');

            // Debug: uncomment next line to see cookie status in console
            // console.log('Intro cookie check:', { hasSeenIntro: hasSeenIntro, cookies: allCookies });

            // Handle reset_intro parameter
            if (resetIntro) {
                document.cookie = 'mwo_intro_seen=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; SameSite=Lax';
                window.location.href = '<?php echo esc_url( home_url( '/' ) ); ?>';
                return;
            }

            // Handle show_intro parameter
            if (showIntro) {
                document.cookie = 'mwo_intro_seen=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; SameSite=Lax';
                window.location.href = '<?php echo esc_url( $intro_url ); ?>';
                return;
            }

            // Redirect to intro if not seen and not skipped
            if (!hasSeenIntro && !skipIntro) {
                window.location.href = '<?php echo esc_url( $intro_url ); ?>';
            }
        })();
        </script>
        <?php
    }
}
add_action( 'wp_head', 'mwo_intro_redirect_script', 1 );

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

    // Menu accent color
    if ( isset( $input['menu_accent_color'] ) ) {
        $sanitized['menu_accent_color'] = sanitize_hex_color( $input['menu_accent_color'] );
        if ( empty( $sanitized['menu_accent_color'] ) ) {
            $sanitized['menu_accent_color'] = '#c34143';
        }
    } else {
        $sanitized['menu_accent_color'] = '#c34143';
    }

    // Link color
    if ( isset( $input['link_color'] ) ) {
        $sanitized['link_color'] = sanitize_hex_color( $input['link_color'] );
        if ( empty( $sanitized['link_color'] ) ) {
            $sanitized['link_color'] = '#c34143';
        }
    } else {
        $sanitized['link_color'] = '#c34143';
    }

    // Checkboxes
    $sanitized['sticky_header'] = isset( $input['sticky_header'] ) ? 1 : 0;
    $sanitized['darkmode'] = isset( $input['darkmode'] ) ? 1 : 0;
    $sanitized['show_site_title'] = isset( $input['show_site_title'] ) ? 1 : 0;
    $sanitized['show_tagline'] = isset( $input['show_tagline'] ) ? 1 : 0;
    $sanitized['disable_page_titles'] = isset( $input['disable_page_titles'] ) ? 1 : 0;
    $sanitized['disable_footer_credits'] = isset( $input['disable_footer_credits'] ) ? 1 : 0;
    $sanitized['lightbox_captions'] = isset( $input['lightbox_captions'] ) ? 1 : 0;
    $sanitized['enable_intro'] = isset( $input['enable_intro'] ) ? 1 : 0;
    $sanitized['enable_masonry'] = isset( $input['enable_masonry'] ) ? 1 : 0;
    $sanitized['content_protection'] = isset( $input['content_protection'] ) ? 1 : 0;
    $sanitized['auto_resize_images'] = isset( $input['auto_resize_images'] ) ? 1 : 0;
    $sanitized['disable_extra_sizes'] = isset( $input['disable_extra_sizes'] ) ? 1 : 0;
    $sanitized['optimize_srcset'] = isset( $input['optimize_srcset'] ) ? 1 : 0;

    // Max image size
    if ( isset( $input['max_image_size'] ) ) {
        $sanitized['max_image_size'] = absint( $input['max_image_size'] );
        if ( $sanitized['max_image_size'] < 800 ) {
            $sanitized['max_image_size'] = 800;
        } elseif ( $sanitized['max_image_size'] > 5000 ) {
            $sanitized['max_image_size'] = 5000;
        }
    } else {
        $sanitized['max_image_size'] = 2400;
    }

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

    // Content container width
    if ( isset( $input['content_container_width'] ) ) {
        $sanitized['content_container_width'] = absint( $input['content_container_width'] );
        if ( $sanitized['content_container_width'] < 400 ) {
            $sanitized['content_container_width'] = 400;
        } elseif ( $sanitized['content_container_width'] > 2000 ) {
            $sanitized['content_container_width'] = 2000;
        }
    } else {
        $sanitized['content_container_width'] = 1170;
    }

    // Social media URLs
    $sanitized = mwo_sanitize_social_urls( $input, $sanitized );

    return $sanitized;
}
