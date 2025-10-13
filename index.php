<?php
/**
 * Main template file
 *
 * @package Mijn_Werk_Online
 */

get_header(); ?>

<main class="site-content">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="entry-meta">
                        <span><?php echo get_the_date(); ?></span>
                        <span><?php the_author(); ?></span>
                    </div>
                </header>

                <div class="entry-content">
                    <?php the_excerpt(); ?>
                </div>
            </article>
            <?php
        endwhile;

        the_posts_pagination();
    else :
        ?>
        <p><?php esc_html_e( 'Geen berichten gevonden.', 'mwo' ); ?></p>
        <?php
    endif;
    ?>
</main>

<?php get_footer(); ?>
