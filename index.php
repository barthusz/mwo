<?php
/**
 * Main template file (blog archive)
 *
 * @package Mijn_Werk_Online
 */

get_header(); ?>

<main class="site-content">
    <?php
    if ( have_posts() ) :
        ?>
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e( 'Blog', 'mwo' ); ?></h1>
        </header>
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="entry-meta">
                        <span class="posted-on"><?php echo get_the_date(); ?></span>
                        <span class="byline"> <?php esc_html_e( 'door', 'mwo' ); ?> <?php the_author(); ?></span>
                    </div>
                </header>

                <div class="entry-content">
                    <?php the_excerpt(); ?>
                    <a href="<?php the_permalink(); ?>" class="more-link"><?php esc_html_e( 'Lees meer', 'mwo' ); ?></a>
                </div>
            </article>
            <?php
        endwhile;

        the_posts_pagination( array(
            'mid_size'  => 2,
            'prev_text' => __( '&larr; Vorige', 'mwo' ),
            'next_text' => __( 'Volgende &rarr;', 'mwo' ),
        ) );
    else :
        ?>
        <p><?php esc_html_e( 'Geen berichten gevonden.', 'mwo' ); ?></p>
        <?php
    endif;
    ?>
</main>

<?php get_footer(); ?>
