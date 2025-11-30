<?php
/**
 * Template Functions
 * 
 * Custom functions for theme templates.
 *
 * @package Base_Theme
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get the theme's custom logo or site title.
 *
 * @return void
 */
function base_theme_site_branding() {
    if (has_custom_logo()) {
        the_custom_logo();
    } else {
        ?>
        <h1 class="site-title">
            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                <?php bloginfo('name'); ?>
            </a>
        </h1>
        <?php
    }
}

/**
 * Display posted on date.
 *
 * @return void
 */
function base_theme_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    
    if (get_the_time('U') !== get_the_modified_time('U')) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf(
        $time_string,
        esc_attr(get_the_date(DATE_W3C)),
        esc_html(get_the_date()),
        esc_attr(get_the_modified_date(DATE_W3C)),
        esc_html(get_the_modified_date())
    );

    printf(
        '<span class="posted-on">%s</span>',
        '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
    );
}

/**
 * Display post author.
 *
 * @return void
 */
function base_theme_posted_by() {
    printf(
        '<span class="byline">%s <span class="author vcard"><a class="url fn n" href="%s">%s</a></span></span>',
        esc_html_x('by', 'post author', 'base-theme'),
        esc_url(get_author_posts_url(get_the_author_meta('ID'))),
        esc_html(get_the_author())
    );
}

/**
 * Display post categories.
 *
 * @return void
 */
function base_theme_entry_categories() {
    if ('post' === get_post_type()) {
        $categories_list = get_the_category_list(esc_html__(', ', 'base-theme'));
        if ($categories_list) {
            printf(
                '<span class="cat-links">%s</span>',
                $categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            );
        }
    }
}

/**
 * Display post tags.
 *
 * @return void
 */
function base_theme_entry_tags() {
    if ('post' === get_post_type()) {
        $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'base-theme'));
        if ($tags_list) {
            printf(
                '<span class="tags-links">%s</span>',
                $tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            );
        }
    }
}

/**
 * Check if post has thumbnail.
 *
 * @return bool
 */
function base_theme_has_post_thumbnail() {
    return has_post_thumbnail() && !post_password_required();
}

/**
 * Display responsive post thumbnail.
 *
 * @param string $size Image size.
 * @return void
 */
function base_theme_post_thumbnail($size = 'post-thumbnail') {
    if (base_theme_has_post_thumbnail()) {
        ?>
        <div class="post-thumbnail">
            <?php
            if (is_singular()) {
                the_post_thumbnail($size);
            } else {
                ?>
                <a href="<?php echo esc_url(get_permalink()); ?>" aria-hidden="true" tabindex="-1">
                    <?php the_post_thumbnail($size); ?>
                </a>
                <?php
            }
            ?>
        </div>
        <?php
    }
}

/**
 * Get reading time estimate.
 *
 * @param int $post_id Post ID.
 * @return int Reading time in minutes.
 */
function base_theme_reading_time($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed
    
    return $reading_time;
}

/**
 * Display breadcrumbs.
 *
 * @return void
 */
function base_theme_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    $separator = '<span class="separator">/</span>';
    
    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'base-theme') . '">';
    echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'base-theme') . '</a>' . $separator;

    if (is_category() || is_single()) {
        $categories = get_the_category();
        if ($categories) {
            echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>' . $separator;
        }
        if (is_single()) {
            echo '<span class="current">' . esc_html(get_the_title()) . '</span>';
        }
    } elseif (is_page()) {
        echo '<span class="current">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_search()) {
        echo '<span class="current">' . esc_html__('Search results', 'base-theme') . '</span>';
    } elseif (is_404()) {
        echo '<span class="current">' . esc_html__('404 Not Found', 'base-theme') . '</span>';
    }

    echo '</nav>';
}
