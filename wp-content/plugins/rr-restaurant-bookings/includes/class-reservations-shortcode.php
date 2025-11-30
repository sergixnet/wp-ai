<?php
/**
 * Clase para el shortcode del formulario de reservas
 */

if (!defined('ABSPATH')) exit;

class RR_Shortcode {
    
    public function __construct() {
        add_shortcode('restaurant_reservations', array($this, 'render_shortcode'));
        add_action('wp_ajax_rr_get_available_slots', array($this, 'ajax_get_available_slots'));
        add_action('wp_ajax_nopriv_rr_get_available_slots', array($this, 'ajax_get_available_slots'));
        add_action('wp_ajax_rr_submit_reservation', array($this, 'ajax_submit_reservation'));
        add_action('wp_ajax_nopriv_rr_submit_reservation', array($this, 'ajax_submit_reservation'));
    }
    
    /**
     * Renderizar shortcode [restaurant_reservations]
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Reserva tu Mesa'
        ), $atts);
        
        // Asegurar que los scripts y estilos están cargados
        wp_enqueue_style('rr-frontend-css', RR_PLUGIN_URL . 'assets/css/frontend.css', array(), RR_VERSION);
        wp_enqueue_script('rr-frontend-js', RR_PLUGIN_URL . 'assets/js/reservations.js', array('jquery'), RR_VERSION, true);
        
        // Pasar datos al JavaScript
        wp_localize_script('rr-frontend-js', 'rrFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rr-frontend-nonce'),
            'minAdvanceHours' => get_option('rr_min_advance_hours', 2)
        ));
        
        ob_start();
        ?>
        <div class="rr-reservation-form-container">
            <h2><?php echo esc_html($atts['title']); ?></h2>
            
            <div id="rr-messages"></div>
            
            <form id="rr-reservation-form" class="rr-form">
                <div class="rr-form-row">
                    <div class="rr-form-field">
                        <label for="rr-customer-name">Nombre completo *</label>
                        <input type="text" id="rr-customer-name" name="customer_name" required>
                    </div>
                    
                    <div class="rr-form-field">
                        <label for="rr-customer-email">Email *</label>
                        <input type="email" id="rr-customer-email" name="customer_email" required>
                    </div>
                </div>
                
                <div class="rr-form-row">
                    <div class="rr-form-field">
                        <label for="rr-customer-phone">Teléfono *</label>
                        <input type="tel" id="rr-customer-phone" name="customer_phone" required>
                    </div>
                    
                    <div class="rr-form-field">
                        <label for="rr-party-size">Número de comensales *</label>
                        <select id="rr-party-size" name="party_size" required>
                            <option value="">Selecciona...</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> persona<?php echo $i > 1 ? 's' : ''; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                
                <div class="rr-form-row">
                    <div class="rr-form-field">
                        <label for="rr-reservation-date">Fecha *</label>
                        <input type="date" id="rr-reservation-date" name="reservation_date" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="rr-form-field">
                        <label for="rr-reservation-time">Hora *</label>
                        <select id="rr-reservation-time" name="reservation_time" required disabled>
                            <option value="">Primero selecciona fecha y comensales</option>
                        </select>
                        <span class="rr-loading" style="display:none;">Cargando...</span>
                    </div>
                </div>
                
                <div class="rr-form-field">
                    <label for="rr-special-requests">Peticiones especiales (opcional)</label>
                    <textarea id="rr-special-requests" name="special_requests" rows="3"></textarea>
                </div>
                
                <div class="rr-form-actions">
                    <button type="submit" class="rr-submit-btn">Reservar Mesa</button>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * AJAX: Obtener franjas horarias disponibles
     */
    public function ajax_get_available_slots() {
        check_ajax_referer('rr-frontend-nonce', 'nonce');
        
        $date = sanitize_text_field($_POST['date']);
        $party_size = absint($_POST['party_size']);
        
        // Validar que la fecha no sea pasada
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            wp_send_json_error(array('message' => 'No se pueden hacer reservas para fechas pasadas.'));
        }
        
        // Verificar si es día cerrado
        if (RR_Calendar::is_closed_date($date)) {
            wp_send_json_error(array('message' => 'El restaurante está cerrado ese día.'));
        }
        
        $slots = RR_Reservations_Data::get_available_time_slots($date, $party_size);
        
        if (empty($slots)) {
            wp_send_json_error(array('message' => 'No hay franjas horarias disponibles para esa fecha.'));
        }
        
        wp_send_json_success(array('slots' => $slots));
    }
    
    /**
     * AJAX: Enviar reserva
     */
    public function ajax_submit_reservation() {
        check_ajax_referer('rr-frontend-nonce', 'nonce');
        
        // Validar datos
        $data = array(
            'customer_name' => sanitize_text_field($_POST['customer_name']),
            'customer_email' => sanitize_email($_POST['customer_email']),
            'customer_phone' => sanitize_text_field($_POST['customer_phone']),
            'reservation_date' => sanitize_text_field($_POST['reservation_date']),
            'reservation_time' => sanitize_text_field($_POST['reservation_time']),
            'party_size' => absint($_POST['party_size']),
            'special_requests' => sanitize_textarea_field($_POST['special_requests'])
        );
        
        // Validar email
        if (!is_email($data['customer_email'])) {
            wp_send_json_error(array('message' => 'Email no válido.'));
        }
        
        // Buscar mesa disponible
        $tables = RR_Tables_Data::get_tables_by_capacity($data['party_size']);
        $available_table = null;
        
        foreach ($tables as $table) {
            if (RR_Reservations_Data::check_availability($table->id, $data['reservation_date'], $data['reservation_time'])) {
                $available_table = $table;
                break;
            }
        }
        
        if (!$available_table) {
            wp_send_json_error(array('message' => 'No hay mesas disponibles en ese horario.'));
        }
        
        // Añadir tabla y estado
        $data['table_id'] = $available_table->id;
        $data['status'] = get_option('rr_auto_confirm', '0') === '1' ? 'confirmed' : 'pending';
        
        // Crear reserva
        $reservation_id = RR_Reservations_Data::create_reservation($data);
        
        if (!$reservation_id) {
            wp_send_json_error(array('message' => 'Error al crear la reserva. Por favor, inténtalo de nuevo.'));
        }
        
        // Enviar email de confirmación
        RR_Email::send_confirmation($reservation_id);
        
        $message = $data['status'] === 'confirmed' 
            ? '¡Reserva confirmada! Recibirás un email de confirmación.' 
            : '¡Reserva recibida! Te enviaremos un email cuando sea confirmada.';
        
        wp_send_json_success(array('message' => $message));
    }
}
