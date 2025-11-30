<?php
/**
 * Clase para la página de configuración del plugin
 */

if (!defined('ABSPATH')) exit;

class RR_Settings_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Añadir página al menú
     */
    public function add_menu_page() {
        add_submenu_page(
            'restaurant-reservations',
            'Configuración de Reservas',
            'Configuración',
            'manage_options',
            'restaurant-reservations-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Registrar configuraciones
     */
    public function register_settings() {
        // Sección de horarios
        add_settings_section(
            'rr_schedule_section',
            'Horarios del Restaurante',
            array($this, 'render_schedule_section'),
            'restaurant-reservations-settings'
        );
        
        register_setting('rr_settings', 'rr_opening_time');
        add_settings_field('rr_opening_time', 'Hora de Apertura', array($this, 'render_opening_time_field'), 'restaurant-reservations-settings', 'rr_schedule_section');
        
        register_setting('rr_settings', 'rr_closing_time');
        add_settings_field('rr_closing_time', 'Hora de Cierre', array($this, 'render_closing_time_field'), 'restaurant-reservations-settings', 'rr_schedule_section');
        
        register_setting('rr_settings', 'rr_closed_days');
        add_settings_field('rr_closed_days', 'Días Cerrados', array($this, 'render_closed_days_field'), 'restaurant-reservations-settings', 'rr_schedule_section');
        
        // Sección de reservas
        add_settings_section(
            'rr_booking_section',
            'Configuración de Reservas',
            array($this, 'render_booking_section'),
            'restaurant-reservations-settings'
        );
        
        register_setting('rr_settings', 'rr_reservation_duration');
        add_settings_field('rr_reservation_duration', 'Duración de Reserva', array($this, 'render_duration_field'), 'restaurant-reservations-settings', 'rr_booking_section');
        
        register_setting('rr_settings', 'rr_time_slot_interval');
        add_settings_field('rr_time_slot_interval', 'Intervalo de Franjas Horarias', array($this, 'render_interval_field'), 'restaurant-reservations-settings', 'rr_booking_section');
        
        register_setting('rr_settings', 'rr_min_advance_hours');
        add_settings_field('rr_min_advance_hours', 'Tiempo Mínimo de Antelación', array($this, 'render_min_advance_field'), 'restaurant-reservations-settings', 'rr_booking_section');
        
        register_setting('rr_settings', 'rr_auto_confirm');
        add_settings_field('rr_auto_confirm', 'Confirmación Automática', array($this, 'render_auto_confirm_field'), 'restaurant-reservations-settings', 'rr_booking_section');
        
        // Sección de emails
        add_settings_section(
            'rr_email_section',
            'Configuración de Emails',
            array($this, 'render_email_section'),
            'restaurant-reservations-settings'
        );
        
        register_setting('rr_settings', 'rr_admin_email');
        add_settings_field('rr_admin_email', 'Email del Administrador', array($this, 'render_admin_email_field'), 'restaurant-reservations-settings', 'rr_email_section');
    }
    
    /**
     * Renderizar página de configuración
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Configuración de Restaurant Reservations</h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('rr_settings');
                do_settings_sections('restaurant-reservations-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    // Renderizado de secciones
    public function render_schedule_section() {
        echo '<p>Configura los horarios de apertura y cierre del restaurante.</p>';
    }
    
    public function render_booking_section() {
        echo '<p>Configura el comportamiento de las reservas.</p>';
    }
    
    public function render_email_section() {
        echo '<p>Configura las notificaciones por email.</p>';
    }
    
    // Renderizado de campos
    public function render_opening_time_field() {
        $value = get_option('rr_opening_time', '12:00');
        echo '<input type="time" name="rr_opening_time" value="' . esc_attr($value) . '">';
    }
    
    public function render_closing_time_field() {
        $value = get_option('rr_closing_time', '23:00');
        echo '<input type="time" name="rr_closing_time" value="' . esc_attr($value) . '">';
    }
    
    public function render_closed_days_field() {
        $closed_days = get_option('rr_closed_days', array());
        $days = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
        
        echo '<fieldset>';
        foreach ($days as $index => $day) {
            $checked = in_array($index, (array)$closed_days) ? 'checked' : '';
            echo '<label style="display:block;margin:5px 0;">';
            echo '<input type="checkbox" name="rr_closed_days[]" value="' . $index . '" ' . $checked . '> ' . $day;
            echo '</label>';
        }
        echo '</fieldset>';
        echo '<p class="description">Marca los días en los que el restaurante está cerrado.</p>';
    }
    
    public function render_duration_field() {
        $value = get_option('rr_reservation_duration', '120');
        echo '<input type="number" name="rr_reservation_duration" value="' . esc_attr($value) . '" min="30" max="480" step="30"> minutos';
        echo '<p class="description">Duración predeterminada de cada reserva (ej: 120 minutos = 2 horas).</p>';
    }
    
    public function render_interval_field() {
        $value = get_option('rr_time_slot_interval', '30');
        echo '<select name="rr_time_slot_interval">';
        echo '<option value="15" ' . selected($value, '15', false) . '>15 minutos</option>';
        echo '<option value="30" ' . selected($value, '30', false) . '>30 minutos</option>';
        echo '<option value="60" ' . selected($value, '60', false) . '>60 minutos</option>';
        echo '</select>';
        echo '<p class="description">Intervalo entre franjas horarias disponibles.</p>';
    }
    
    public function render_min_advance_field() {
        $value = get_option('rr_min_advance_hours', '2');
        echo '<input type="number" name="rr_min_advance_hours" value="' . esc_attr($value) . '" min="0" max="72"> horas';
        echo '<p class="description">Tiempo mínimo de antelación para hacer una reserva.</p>';
    }
    
    public function render_auto_confirm_field() {
        $value = get_option('rr_auto_confirm', '0');
        echo '<label>';
        echo '<input type="checkbox" name="rr_auto_confirm" value="1" ' . checked($value, '1', false) . '> Confirmar reservas automáticamente';
        echo '</label>';
        echo '<p class="description">Si está activado, las reservas se confirman automáticamente sin necesidad de aprobación manual.</p>';
    }
    
    public function render_admin_email_field() {
        $value = get_option('rr_admin_email', get_option('admin_email'));
        echo '<input type="email" name="rr_admin_email" value="' . esc_attr($value) . '" class="regular-text">';
        echo '<p class="description">Email donde se recibirán las notificaciones de nuevas reservas.</p>';
    }
}
