<?php
/**
 * The template for displaying archive pages
 *
 * @package Base_Theme
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php if (have_posts()) : ?>

            <header class="page-header">
                <?php
                the_archive_title('<h1 class="page-title">', '</h1>');
                the_archive_description('<div class="archive-description">', '</div>');
                ?>
            </header><!-- .page-header -->

            <div class="row">
                <?php
                /* Start the Loop */
                while (have_posts()) :
                    the_post();
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <?php get_template_part('template-parts/content', get_post_type()); ?>
                    </div>
                    <?php
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
