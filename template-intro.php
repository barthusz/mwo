<?php
/**
 * Template Name: Intro Screen
 *
 * This template is used to display the intro screen
 * It should be set as a standalone page
 *
 * @package Mijn_Werk_Online
 */

// Prevent caching (works with all cache plugins)
if ( ! defined( 'DONOTCACHEPAGE' ) ) {
    define( 'DONOTCACHEPAGE', true );
}

// Get theme options
$options = get_option( 'mwo_options' );
$intro_images = isset( $options['intro_images'] ) ? $options['intro_images'] : array();
$intro_button_text = isset( $options['intro_button_text'] ) ? $options['intro_button_text'] : 'VIEW MY WORK';

// Get the homepage URL
$homepage_url = home_url( '/' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <script>
    // Set cookie immediately when intro page loads (JavaScript ensures it's available immediately)
    (function() {
        // Set session cookie (expires when browser is closed)
        document.cookie = "mwo_intro_seen=1; path=/; SameSite=Lax";
    })();
    </script>
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
        <a href="<?php echo esc_url( $homepage_url ); ?>" class="intro-button" id="intro-button">
            <?php echo esc_html( $intro_button_text ); ?>
        </a>
    </div>
</div>

<?php wp_footer(); ?>
<script>
// Ensure cookie is set when button is clicked
document.addEventListener('DOMContentLoaded', function() {
    var button = document.getElementById('intro-button');
    if (button) {
        button.addEventListener('click', function(e) {
            // Make absolutely sure the session cookie is set
            document.cookie = "mwo_intro_seen=1; path=/; SameSite=Lax";

            // Small delay to ensure cookie is written
            e.preventDefault();
            setTimeout(function() {
                window.location.href = button.href;
            }, 100);
        });
    }
});
</script>
</body>
</html>
