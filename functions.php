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
