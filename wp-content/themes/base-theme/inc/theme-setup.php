<?php
/**
 * Theme Setup
 *
 * @package Base_Theme
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function base_theme_setup() {
    /*
     * Make theme available for translation.
     */
    load_theme_textdomain('base-theme', BASE_THEME_DIR . '/languages');

    /*
     * Let WordPress manage the document title.
     */
    add_theme_support('title-tag');

    /*
     * Enable support for Post Thumbnails on posts and pages.
     */
    add_theme_support('post-thumbnails');
    
    // Custom image sizes
    add_image_size('base-theme-featured', 1200, 630, true);
    add_image_size('base-theme-thumbnail', 600, 400, true);

    /*
     * Register navigation menus.
     */
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'base-theme'),
        'footer'  => esc_html__('Footer Menu', 'base-theme'),
    ));

    /*
     * Switch default core markup to output valid HTML5.
     */
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    /*
     * Add support for core custom logo.
     */
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    /*
     * Add theme support for selective refresh for widgets.
     */
    add_theme_support('customize-selective-refresh-widgets');

    /*
     * Add support for Block Styles.
     */
    add_theme_support('wp-block-styles');

    /*
     * Add support for full and wide align images.
     */
    add_theme_support('align-wide');

    /*
     * Add support for editor styles.
     */
    add_theme_support('editor-styles');
    add_editor_style('assets/dist/css/editor-style.css');

    /*
     * Add support for responsive embedded content.
     */
    add_theme_support('responsive-embeds');

    /*
     * Add support for post formats.
     */
    add_theme_support('post-formats', array(
        'aside',
        'gallery',
        'link',
        'image',
        'quote',
        'video',
        'audio',
    ));

    /*
     * Add support for custom background.
     */
    add_theme_support('custom-background', array(
        'default-color' => 'ffffff',
    ));
}
add_action('after_setup_theme', 'base_theme_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 */
function base_theme_content_width() {
    $GLOBALS['content_width'] = apply_filters('base_theme_content_width', 1200);
}
add_action('after_setup_theme', 'base_theme_content_width', 0);

/**
 * Register widget areas.
 */
function base_theme_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'base-theme'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here to appear in your sidebar.', 'base-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Footer', 'base-theme'),
        'id'            => 'footer-1',
        'description'   => esc_html__('Add widgets here to appear in your footer.', 'base-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'base_theme_widgets_init');
