<?php
/**
 * Template Name: Intro Screen
 *
 * This template is used to display the intro screen
 * It should be set as a standalone page
 *
 * @package Mijn_Werk_Online
 */

// Get theme options
$options = get_option( 'mwo_options' );
$intro_images = isset( $options['intro_images'] ) ? $options['intro_images'] : array();
$intro_button_text = isset( $options['intro_button_text'] ) ? $options['intro_button_text'] : 'VIEW MY WORK';

// Get the homepage URL
$homepage_url = home_url( '/?skip_intro=1' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'intro-screen-active' ); ?>>
<?php wp_body_open(); ?>

<div class="intro-screen">
    <div class="intro-background">
        <?php
        if ( ! empty( $intro_images ) && is_array( $intro_images ) ) {
            foreach ( $intro_images as $index => $image_id ) {
                $image_url = wp_get_attachment_image_url( $image_id, 'full' );
                if ( $image_url ) {
                    $active_class = $index === 0 ? 'active' : '';
                    printf(
                        '<div class="intro-slide %s" style="background-image: url(%s);"></div>',
                        esc_attr( $active_class ),
                        esc_url( $image_url )
                    );
                }
            }
        }
        ?>
        <div class="intro-overlay"></div>
    </div>

    <div class="intro-content">
        <h1 class="intro-title"><?php bloginfo( 'name' ); ?></h1>
        <?php
        $description = get_bloginfo( 'description', 'display' );
        if ( $description ) {
            printf( '<p class="intro-tagline">%s</p>', esc_html( $description ) );
        }
        ?>
        <a href="<?php echo esc_url( $homepage_url ); ?>" class="intro-button">
            <?php echo esc_html( $intro_button_text ); ?>
        </a>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
