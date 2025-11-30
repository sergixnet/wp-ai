<?php
/**
 * Component Loader Class
 * 
 * Loads reusable template components with parameters.
 *
 * @package Base_Theme
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Base_Theme_Component_Loader {
    
    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Components directory.
     *
     * @var string
     */
    private $components_dir = 'template-parts/components';

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
        // Component loader is ready
    }

    /**
     * Load a component with parameters.
     *
     * @param string $component Component name (without .php extension).
     * @param array  $args      Arguments to pass to the component.
     * @return void
     */
    public static function load($component, $args = array()) {
        $instance = self::get_instance();
        $component_path = $instance->components_dir . '/' . $component . '.php';
        
        if (locate_template($component_path)) {
            // Extract args to make them available as variables
            if (!empty($args) && is_array($args)) {
                extract($args); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
            }
            
            get_template_part($instance->components_dir . '/' . $component, null, $args);
        }
    }

    /**
     * Check if component exists.
     *
     * @param string $component Component name.
     * @return bool
     */
    public static function exists($component) {
        $instance = self::get_instance();
        $component_path = $instance->components_dir . '/' . $component . '.php';
        
        return (bool) locate_template($component_path);
    }

    /**
     * Get component path.
     *
     * @param string $component Component name.
     * @return string|false
     */
    public static function get_path($component) {
        $instance = self::get_instance();
        $component_path = $instance->components_dir . '/' . $component . '.php';
        
        return locate_template($component_path);
    }
}
