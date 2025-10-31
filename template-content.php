<?php
/**
 * Template Name: Content Container
 * Template for pages with centered content (max 1170px, excluding galleries)
 *
 * @package Mijn_Werk_Online
 */

get_header();

// Get theme options
$options = get_option( 'mwo_options' );
$disable_page_titles = isset( $options['disable_page_titles'] ) && $options['disable_page_titles'] ? true : false;
?>

<main class="site-content content-container-template">
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php if ( ! $disable_page_titles ) : ?>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>
            <?php endif; ?>

            <div class="entry-content">
                <?php
                the_content();

                wp_link_pages( array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pagina\'s:', 'mwo' ),
                    'after'  => '</div>',
                ) );
                ?>
            </div>
        </article>
        <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
