<?php
/**
 * Base Theme Functions
 *
 * @package Base_Theme
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Define theme constants
 */
define('BASE_THEME_VERSION', '1.0.0');
define('BASE_THEME_DIR', get_template_directory());
define('BASE_THEME_URI', get_template_directory_uri());

/**
 * Load theme classes and functions
 */
require_once BASE_THEME_DIR . '/inc/class-assets-manager.php';
require_once BASE_THEME_DIR . '/inc/class-component-loader.php';
require_once BASE_THEME_DIR . '/inc/theme-setup.php';
require_once BASE_THEME_DIR . '/inc/template-functions.php';
require_once BASE_THEME_DIR . '/inc/template-tags.php';

/**
 * Initialize theme
 */
function base_theme_init() {
    // Initialize Assets Manager
    Base_Theme_Assets_Manager::get_instance();
    
    // Initialize Component Loader
    Base_Theme_Component_Loader::get_instance();
}
add_action('after_setup_theme', 'base_theme_init');

/**
 * Load theme textdomain
 */
function base_theme_load_textdomain() {
    load_theme_textdomain('base-theme', BASE_THEME_DIR . '/languages');
}
add_action('after_setup_theme', 'base_theme_load_textdomain');
