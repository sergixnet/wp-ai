<?php
/**
 * Template part for displaying single posts
 *
 * @package Base_Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

        <div class="entry-meta">
            <?php
            base_theme_posted_on();
            base_theme_posted_by();
            base_theme_entry_categories();
            ?>
        </div><!-- .entry-meta -->
    </header><!-- .entry-header -->

    <?php base_theme_post_thumbnail(); ?>

    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages(array(
            'before' => '<div class="page-links">' . esc_html__('Pages:', 'base-theme'),
            'after'  => '</div>',
        ));
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php base_theme_entry_tags(); ?>
        
        <?php
        // Author bio
        if (get_the_author_meta('description')) :
            ?>
            <div class="author-bio">
                <div class="author-avatar">
                    <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                </div>
                <div class="author-info">
                    <h3 class="author-name">
                        <?php echo esc_html(get_the_author()); ?>
                    </h3>
                    <div class="author-description">
                        <?php echo wp_kses_post(get_the_author_meta('description')); ?>
                    </div>
                    <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="author-link">
                        <?php esc_html_e('View all posts', 'base-theme'); ?>
                    </a>
                </div>
            </div>
            <?php
        endif;
        ?>
    </footer><!-- .entry-footer -->

    <?php
    // Post navigation
    the_post_navigation(array(
        'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'base-theme') . '</span> <span class="nav-title">%title</span>',
        'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'base-theme') . '</span> <span class="nav-title">%title</span>',
    ));
    ?>
</article><!-- #post-<?php the_ID(); ?> -->
