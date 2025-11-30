<?php
/**
 * The template for displaying search results pages
 *
 * @package Base_Theme
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php if (have_posts()) : ?>

            <header class="page-header">
                <h1 class="page-title">
                    <?php
                    /* translators: %s: search query. */
                    printf(esc_html__('Search Results for: %s', 'base-theme'), '<span>' . get_search_query() . '</span>');
                    ?>
                </h1>
            </header><!-- .page-header -->

            <div class="search-results">
                <?php
                /* Start the Loop */
                while (have_posts()) :
                    the_post();

                    /**
                     * Run the loop for the search to output the results.
                     * If you want to overload this in a child theme then include a file
                     * called content-search.php and that will be used instead.
                     */
                    get_template_part('template-parts/content', 'search');

                endwhile;
                ?>
            </div>

            <?php
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('&larr; Previous', 'base-theme'),
                'next_text' => __('Next &rarr;', 'base-theme'),
            ));

        else :

            get_template_part('template-parts/content', 'none');

        endif;
        ?>
    </div>
</main><!-- #main -->

<?php
get_footer();
