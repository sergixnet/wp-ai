<?php
/**
 * Plugin Name: RR Restaurant Bookings
 * Plugin URI: https://wp-ai.dev
 * Description: Sistema completo de gestión de reservas para restaurantes con calendario, gestión de mesas y notificaciones por email
 * Version: 1.0.0
 * Author: Development Team
 * Author URI: https://wp-ai.dev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rr-restaurant-bookings
 * Domain Path: /languages
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) exit;

// Definir constantes del plugin
define('RR_VERSION', '1.0.0');
define('RR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RR_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Cargar archivos del plugin
 */
function rr_load_plugin_files() {
    // Clases de datos
    require_once RR_PLUGIN_DIR . 'includes/class-reservations-tables-data.php';
    require_once RR_PLUGIN_DIR . 'includes/class-reservations-data.php';
    require_once RR_PLUGIN_DIR . 'includes/class-reservations-calendar.php';
    require_once RR_PLUGIN_DIR . 'includes/class-reservations-email.php';
    
    // Clases de administración
    if (is_admin()) {
        require_once RR_PLUGIN_DIR . 'admin/class-reservations-tables-admin.php';
        require_once RR_PLUGIN_DIR . 'admin/class-reservations-admin.php';
        require_once RR_PLUGIN_DIR . 'admin/class-reservations-calendar-admin.php';
        require_once RR_PLUGIN_DIR . 'admin/class-reservations-settings.php';
    }
    
    // Clases de frontend
    require_once RR_PLUGIN_DIR . 'includes/class-reservations-shortcode.php';
}

/**
 * Inicializar el plugin
 */
function rr_init_plugin() {
    // Cargar archivos
    rr_load_plugin_files();
    
    // Inicializar clases de administración
    if (is_admin()) {
        new RR_Tables_Admin();
        new RR_Reservations_Admin();
        new RR_Calendar_Admin();
        new RR_Settings_Admin();
    }
    
    // Inicializar frontend
    new RR_Shortcode();
}
add_action('plugins_loaded', 'rr_init_plugin');

/**
 * Activación del plugin - Crear tablas de base de datos
 */
function rr_activate_plugin() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Tabla de mesas
    $table_tables = $wpdb->prefix . 'rr_tables';
    $sql_tables = "CREATE TABLE $table_tables (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        capacity tinyint(3) NOT NULL,
        status varchar(20) DEFAULT 'active',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    // Tabla de reservas
    $table_reservations = $wpdb->prefix . 'rr_reservations';
    $sql_reservations = "CREATE TABLE $table_reservations (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        table_id mediumint(9) NOT NULL,
        customer_name varchar(100) NOT NULL,
        customer_email varchar(100) NOT NULL,
        customer_phone varchar(20) NOT NULL,
        reservation_date date NOT NULL,
        reservation_time time NOT NULL,
        party_size tinyint(3) NOT NULL,
        status varchar(20) DEFAULT 'pending',
        special_requests text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY table_id (table_id),
        KEY reservation_date (reservation_date),
        KEY status (status)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_tables);
    dbDelta($sql_reservations);
    
    // Insertar mesas de ejemplo si es la primera activación
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_tables");
    if ($count == 0) {
        $wpdb->insert($table_tables, array(
            'name' => 'Mesa 1',
            'capacity' => 2,
            'status' => 'active'
        ));
        $wpdb->insert($table_tables, array(
            'name' => 'Mesa 2',
            'capacity' => 4,
            'status' => 'active'
        ));
        $wpdb->insert($table_tables, array(
            'name' => 'Mesa 3',
            'capacity' => 4,
            'status' => 'active'
        ));
        $wpdb->insert($table_tables, array(
            'name' => 'Mesa 4',
            'capacity' => 6,
            'status' => 'active'
        ));
        $wpdb->insert($table_tables, array(
            'name' => 'Mesa 5',
            'capacity' => 8,
            'status' => 'active'
        ));
    }
    
    // Crear opciones por defecto
    add_option('rr_opening_time', '12:00');
    add_option('rr_closing_time', '23:00');
    add_option('rr_reservation_duration', '120'); // 2 horas en minutos
    add_option('rr_min_advance_hours', '2'); // 2 horas de antelación mínima
    add_option('rr_closed_days', array()); // Array de días cerrados (0=domingo, 6=sábado)
    add_option('rr_time_slot_interval', '30'); // Intervalos de 30 minutos
    add_option('rr_auto_confirm', '0'); // Confirmación manual por defecto
    add_option('rr_admin_email', get_option('admin_email'));
    
    // Crear versión de la base de datos
    add_option('rr_db_version', RR_VERSION);
}
register_activation_hook(__FILE__, 'rr_activate_plugin');

/**
 * Desactivación del plugin
 */
function rr_deactivate_plugin() {
    // Limpiar tareas programadas si las hay
    wp_clear_scheduled_hook('rr_send_reminders');
}
register_deactivation_hook(__FILE__, 'rr_deactivate_plugin');

/**
 * Desinstalación del plugin - Eliminar tablas y opciones
 */
function rr_uninstall_plugin() {
    global $wpdb;
    
    // Eliminar tablas
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rr_reservations");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rr_tables");
    
    // Eliminar opciones
    delete_option('rr_opening_time');
    delete_option('rr_closing_time');
    delete_option('rr_reservation_duration');
    delete_option('rr_min_advance_hours');
    delete_option('rr_closed_days');
    delete_option('rr_time_slot_interval');
    delete_option('rr_auto_confirm');
    delete_option('rr_admin_email');
    delete_option('rr_db_version');
}
register_uninstall_hook(__FILE__, 'rr_uninstall_plugin');

/**
 * Cargar estilos y scripts del admin
 */
function rr_admin_enqueue_scripts($hook) {
    // Solo cargar en nuestras páginas
    if (strpos($hook, 'restaurant-reservations') === false) {
        return;
    }
    
    wp_enqueue_style('rr-admin-css', RR_PLUGIN_URL . 'assets/css/admin.css', array(), RR_VERSION);
    wp_enqueue_script('rr-admin-js', RR_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), RR_VERSION, true);
    
    // Pasar datos al JavaScript
    wp_localize_script('rr-admin-js', 'rrAdmin', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('rr-admin-nonce')
    ));
}
add_action('admin_enqueue_scripts', 'rr_admin_enqueue_scripts');

/**
 * Cargar estilos y scripts del frontend
 */
function rr_frontend_enqueue_scripts() {
    // Solo cargar si la página contiene el shortcode
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'restaurant_reservations')) {
        wp_enqueue_style('rr-frontend-css', RR_PLUGIN_URL . 'assets/css/frontend.css', array(), RR_VERSION);
        wp_enqueue_script('rr-frontend-js', RR_PLUGIN_URL . 'assets/js/reservations.js', array('jquery'), RR_VERSION, true);
        
        // Pasar datos al JavaScript
        wp_localize_script('rr-frontend-js', 'rrFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rr-frontend-nonce'),
            'minAdvanceHours' => get_option('rr_min_advance_hours', 2)
        ));
    }
}
add_action('wp_enqueue_scripts', 'rr_frontend_enqueue_scripts');
