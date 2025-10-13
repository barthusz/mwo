<?php
/**
 * Single post template
 *
 * @package Mijn_Werk_Online
 */

get_header(); ?>

<main class="site-content">
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
                <div class="entry-meta">
                    <span class="posted-on"><?php echo get_the_date(); ?></span>
                    <span class="byline"> <?php esc_html_e( 'door', 'mwo' ); ?> <?php the_author(); ?></span>
                </div>
            </header>

            <div class="entry-content">
                <?php
                the_content();

                wp_link_pages( array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pagina\'s:', 'mwo' ),
                    'after'  => '</div>',
                ) );
                ?>
            </div>

            <footer class="entry-footer">
                <?php
                $categories_list = get_the_category_list( ', ' );
                if ( $categories_list ) {
                    printf( '<span class="cat-links">%s %s</span>', esc_html__( 'Categorie:', 'mwo' ), $categories_list );
                }

                $tags_list = get_the_tag_list( '', ', ' );
                if ( $tags_list ) {
                    printf( '<span class="tags-links">%s %s</span>', esc_html__( 'Tags:', 'mwo' ), $tags_list );
                }
                ?>
            </footer>
        </article>

        <?php
        // Comments
        if ( comments_open() || get_comments_number() ) {
            comments_template();
        }
    endwhile;
    ?>
</main>

<?php
get_footer();
