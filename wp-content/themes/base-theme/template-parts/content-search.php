<?php
/**
 * Template part for displaying search results
 *
 * @package Base_Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('search-result'); ?>>
    <header class="entry-header">
        <?php the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>'); ?>

        <?php if ('post' === get_post_type()) : ?>
            <div class="entry-meta">
                <?php base_theme_posted_on(); ?>
            </div><!-- .entry-meta -->
        <?php endif; ?>
    </header><!-- .entry-header -->

    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div><!-- .entry-summary -->

    <footer class="entry-footer">
        <a href="<?php echo esc_url(get_permalink()); ?>" class="read-more">
            <?php esc_html_e('Read more', 'base-theme'); ?> &rarr;
        </a>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
