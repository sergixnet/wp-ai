<?php
/**
 * Assets Manager Class
 * 
 * Handles enqueuing of styles and scripts with proper versioning and dependencies.
 *
 * @package Base_Theme
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Base_Theme_Assets_Manager {
    
    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Get instance of this class.
     *
     * @return object
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_scripts'));
    }

    /**
     * Enqueue frontend styles.
     */
    public function enqueue_styles() {
        // Main stylesheet
        wp_enqueue_style(
            'base-theme-style',
            BASE_THEME_URI . '/assets/dist/css/main.css',
            array(),
            $this->get_file_version('assets/dist/css/main.css')
        );

        // Print styles
        wp_enqueue_style(
            'base-theme-print',
            BASE_THEME_URI . '/assets/dist/css/print.css',
            array('base-theme-style'),
            $this->get_file_version('assets/dist/css/print.css'),
            'print'
        );
    }

    /**
     * Enqueue frontend scripts.
     */
    public function enqueue_scripts() {
        // Main JavaScript
        wp_enqueue_script(
            'base-theme-main',
            BASE_THEME_URI . '/assets/dist/js/main.js',
            array(),
            $this->get_file_version('assets/dist/js/main.js'),
            true
        );

        // Localize script with theme data
        wp_localize_script('base-theme-main', 'baseTheme', array(
            'ajaxUrl'   => admin_url('admin-ajax.php'),
            'nonce'     => wp_create_nonce('base-theme-nonce'),
            'themeUrl'  => BASE_THEME_URI,
            'isRTL'     => is_rtl(),
            'i18n'      => array(
                'loading'   => esc_html__('Loading...', 'base-theme'),
                'error'     => esc_html__('An error occurred', 'base-theme'),
            ),
        ));

        // Enqueue comment reply script
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function enqueue_admin_scripts($hook) {
        wp_enqueue_style(
            'base-theme-admin',
            BASE_THEME_URI . '/assets/dist/css/admin.css',
            array(),
            $this->get_file_version('assets/dist/css/admin.css')
        );

        wp_enqueue_script(
            'base-theme-admin',
            BASE_THEME_URI . '/assets/dist/js/admin.js',
            array('jquery'),
            $this->get_file_version('assets/dist/js/admin.js'),
            true
        );
    }

    /**
     * Enqueue Gutenberg editor scripts and styles.
     */
    public function enqueue_editor_scripts() {
        wp_enqueue_style(
            'base-theme-editor',
            BASE_THEME_URI . '/assets/dist/css/editor-style.css',
            array('wp-edit-blocks'),
            $this->get_file_version('assets/dist/css/editor-style.css')
        );

        wp_enqueue_script(
            'base-theme-editor',
            BASE_THEME_URI . '/assets/dist/js/editor.js',
            array('wp-blocks', 'wp-dom-ready', 'wp-edit-post'),
            $this->get_file_version('assets/dist/js/editor.js'),
            true
        );
    }

    /**
     * Get file version based on file modification time.
     *
     * @param string $file Relative file path.
     * @return string Version string.
     */
    private function get_file_version($file) {
        $file_path = BASE_THEME_DIR . '/' . $file;
        
        if (file_exists($file_path)) {
            return filemtime($file_path);
        }
        
        return BASE_THEME_VERSION;
    }

    /**
     * Conditionally load assets based on template.
     *
     * @param string $handle Script/style handle.
     * @param string $template Template name to check.
     * @return bool
     */
    public function is_template_active($template) {
        return is_page_template($template);
    }
}
