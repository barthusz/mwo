<?php
/**
 * Gallery Captions
 * Add attachment metadata as data attributes to gallery images
 *
 * @package Mijn_Werk_Online
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add caption data to gallery images
 */
function mwo_add_gallery_caption_data( $content ) {
    // Only process on frontend
    if ( is_admin() ) {
        return $content;
    }

    // Check if captions are enabled
    $options = get_option( 'mwo_options' );
    $show_captions = isset( $options['lightbox_captions'] ) && $options['lightbox_captions'] ? true : false;

    if ( ! $show_captions ) {
        return $content;
    }

    // Find all images in gallery blocks
    if ( preg_match_all( '/<img[^>]+class="[^"]*wp-image-(\d+)[^"]*"[^>]*>/i', $content, $matches, PREG_SET_ORDER ) ) {
        foreach ( $matches as $match ) {
            $img_tag = $match[0];
            $attachment_id = $match[1];

            // Get attachment metadata
            $caption = wp_get_attachment_caption( $attachment_id );

            // Only use caption, no fallback to alt or title
            $caption_text = ! empty( $caption ) ? $caption : '';

            // Add data attribute if we have caption text
            if ( ! empty( $caption_text ) ) {
                $caption_text = esc_attr( $caption_text );
                $new_img_tag = str_replace( '<img', '<img data-caption="' . $caption_text . '"', $img_tag );
                $content = str_replace( $img_tag, $new_img_tag, $content );
            }
        }
    }

    return $content;
}
add_filter( 'the_content', 'mwo_add_gallery_caption_data', 20 );
