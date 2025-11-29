<?php
/**
 * Plugin Name: Citas de Humor
 * Plugin URI: https://wp-ai.dev
 * Description: Muestra citas humorísticas aleatorias desde JokeAPI mediante el shortcode [cita_humor] o bloque de Gutenberg
 * Version: 2.1.0
 * Author: WordPress AI
 * Author URI: https://wp-ai.dev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: citas-humor
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('CITAS_HUMOR_VERSION', '2.1.0');
define('CITAS_HUMOR_PATH', plugin_dir_path(__FILE__));
define('CITAS_HUMOR_URL', plugin_dir_url(__FILE__));

// Cargar archivos del plugin
require_once CITAS_HUMOR_PATH . 'includes/class-citas-data.php';
require_once CITAS_HUMOR_PATH . 'includes/class-citas-styles.php';
require_once CITAS_HUMOR_PATH . 'includes/class-citas-shortcode.php';
require_once CITAS_HUMOR_PATH . 'includes/class-citas-block.php';
require_once CITAS_HUMOR_PATH . 'admin/class-citas-admin.php';

// Inicializar el plugin
function citas_humor_init() {
    new Citas_Humor_Shortcode();
    new Citas_Humor_Block();
    
    if (is_admin()) {
        new Citas_Humor_Admin();
    }
}
add_action('plugins_loaded', 'citas_humor_init');

// Activación del plugin
register_activation_hook(__FILE__, 'citas_humor_activate');
function citas_humor_activate() {
    // Establecer opciones por defecto
    if (!get_option('citas_humor_theme')) {
        add_option('citas_humor_theme', 'gradient');
    }
}

// Desactivación del plugin
register_deactivation_hook(__FILE__, 'citas_humor_deactivate');
function citas_humor_deactivate() {
    // Limpiar si es necesario
}
