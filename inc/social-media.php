<?php
/**
 * Social Media Settings
 *
 * @package Mijn_Werk_Online
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get social media platforms
 */
function mwo_get_social_platforms() {
    return array(
        'instagram' => array(
            'label' => __( 'Instagram', 'mwo' ),
            'icon'  => 'fa-brands fa-instagram',
            'color' => '#E4405F',
        ),
        'facebook' => array(
            'label' => __( 'Facebook', 'mwo' ),
            'icon'  => 'fa-brands fa-facebook',
            'color' => '#1877F2',
        ),
        'x' => array(
            'label' => __( 'X (Twitter)', 'mwo' ),
            'icon'  => 'fa-brands fa-x-twitter',
            'color' => '#000000',
        ),
        'linkedin' => array(
            'label' => __( 'LinkedIn', 'mwo' ),
            'icon'  => 'fa-brands fa-linkedin',
            'color' => '#0A66C2',
        ),
        '500px' => array(
            'label' => __( '500px', 'mwo' ),
            'icon'  => 'fa-brands fa-500px',
            'color' => '#0099E5',
        ),
    );
}

/**
 * Register social media settings
 */
function mwo_register_social_settings() {
    add_settings_section(
        'mwo_social_section',
        __( 'Social Media', 'mwo' ),
        'mwo_social_section_callback',
        'mwo-settings'
    );

    $platforms = mwo_get_social_platforms();

    foreach ( $platforms as $key => $platform ) {
        add_settings_field(
            'mwo_social_' . $key,
            $platform['label'],
            'mwo_social_field_callback',
            'mwo-settings',
            'mwo_social_section',
            array(
                'platform' => $key,
                'data'     => $platform,
            )
        );
    }
}
add_action( 'admin_init', 'mwo_register_social_settings', 11 );

/**
 * Social media section callback
 */
function mwo_social_section_callback() {
    echo '<p>' . esc_html__( 'Voeg links toe naar je social media profielen.', 'mwo' ) . '</p>';
}

/**
 * Social media field callback
 */
function mwo_social_field_callback( $args ) {
    $options  = get_option( 'mwo_options' );
    $platform = $args['platform'];
    $data     = $args['data'];
    $value    = isset( $options['social'][ $platform ] ) ? $options['social'][ $platform ] : '';
    ?>
    <div class="mwo-social-field" style="display: flex; align-items: center; gap: 10px;">
        <i class="<?php echo esc_attr( $data['icon'] ); ?>" style="font-size: 24px; color: <?php echo esc_attr( $data['color'] ); ?>; width: 30px;"></i>
        <input
            type="url"
            name="mwo_options[social][<?php echo esc_attr( $platform ); ?>]"
            value="<?php echo esc_url( $value ); ?>"
            class="regular-text"
            placeholder="https://"
        >
    </div>
    <?php
}

/**
 * Sanitize social media URLs
 */
function mwo_sanitize_social_urls( $input, $sanitized ) {
    if ( isset( $input['social'] ) && is_array( $input['social'] ) ) {
        $platforms = mwo_get_social_platforms();
        $sanitized['social'] = array();

        foreach ( $platforms as $key => $platform ) {
            if ( isset( $input['social'][ $key ] ) && ! empty( $input['social'][ $key ] ) ) {
                $sanitized['social'][ $key ] = esc_url_raw( $input['social'][ $key ] );
            }
        }
    }

    return $sanitized;
}
