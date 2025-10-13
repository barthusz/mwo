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
    wp_enqueue_style( 'mwo-style', get_stylesheet_uri(), array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'mwo_enqueue_assets' );

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

    // Checkboxes
    $sanitized['sticky_header'] = isset( $input['sticky_header'] ) ? 1 : 0;
    $sanitized['show_site_title'] = isset( $input['show_site_title'] ) ? 1 : 0;
    $sanitized['show_tagline'] = isset( $input['show_tagline'] ) ? 1 : 0;
    $sanitized['disable_page_titles'] = isset( $input['disable_page_titles'] ) ? 1 : 0;
    $sanitized['disable_footer_credits'] = isset( $input['disable_footer_credits'] ) ? 1 : 0;

    return $sanitized;
}
