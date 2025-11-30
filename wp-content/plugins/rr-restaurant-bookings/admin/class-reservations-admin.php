<?php
/**
 * Clase para la página de administración de reservas
 */

if (!defined('ABSPATH')) exit;

class RR_Reservations_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_page'));
        add_action('admin_post_rr_update_reservation_status', array($this, 'handle_update_status'));
    }
    
    /**
     * Añadir página al menú
     */
    public function add_menu_page() {
        add_submenu_page(
            'restaurant-reservations',
            'Todas las Reservas',
            'Reservas',
            'manage_options',
            'restaurant-reservations-reservations',
            array($this, 'render_reservations_page')
        );
    }
    
    /**
     * Renderizar página de reservas
     */
    public function render_reservations_page() {
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : null;
        $date_filter = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : null;
        
        $filters = array(
            'orderby' => 'reservation_date',
            'order' => 'DESC',
            'limit' => 50
        );
        
        if ($status_filter) {
            $filters['status'] = $status_filter;
        }
        
        if ($date_filter) {
            $filters['date_from'] = $date_filter;
            $filters['date_to'] = $date_filter;
        }
        
        $reservations = RR_Reservations_Data::get_all_reservations($filters);
        
        $statuses = array(
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'cancelled' => 'Cancelada',
            'completed' => 'Completada',
            'no-show' => 'No se presentó'
        );
        ?>
        <div class="wrap">
            <h1>Gestión de Reservas</h1>
            
            <?php if ($date_filter): ?>
                <div class="notice notice-info inline" style="margin: 15px 0;">
                    <p>
                        <strong>Filtrando por fecha:</strong> <?php echo date_i18n('l, j F Y', strtotime($date_filter)); ?>
                        <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-reservations'); ?>" class="button button-small" style="margin-left: 10px;">Quitar filtro</a>
                    </p>
                </div>
            <?php endif; ?>
            
            <ul class="subsubsub">
                <li><a href="<?php echo admin_url('admin.php?page=restaurant-reservations-reservations' . ($date_filter ? '&date=' . $date_filter : '')); ?>" <?php echo !$status_filter ? 'class="current"' : ''; ?>>Todas</a> | </li>
                <?php foreach ($statuses as $key => $label): ?>
                    <li><a href="<?php echo admin_url('admin.php?page=restaurant-reservations-reservations&status=' . $key . ($date_filter ? '&date=' . $date_filter : '')); ?>" <?php echo $status_filter === $key ? 'class="current"' : ''; ?>><?php echo $label; ?></a><?php echo $key !== 'no-show' ? ' | ' : ''; ?></li>
                <?php endforeach; ?>
            </ul>
            
            <table class="wp-list-table widefat fixed striped" style="margin-top:20px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Contacto</th>
                        <th>Fecha y Hora</th>
                        <th>Mesa</th>
                        <th>Comensales</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr><td colspan="8">No hay reservas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $r): ?>
                            <tr>
                                <td><?php echo esc_html($r->id); ?></td>
                                <td><strong><?php echo esc_html($r->customer_name); ?></strong></td>
                                <td><?php echo esc_html($r->customer_email); ?><br><?php echo esc_html($r->customer_phone); ?></td>
                                <td><?php echo esc_html(date_i18n('d/m/Y', strtotime($r->reservation_date))); ?><br><?php echo esc_html(date_i18n('H:i', strtotime($r->reservation_time))); ?></td>
                                <td><?php echo esc_html($r->table_name); ?></td>
                                <td><?php echo esc_html($r->party_size); ?></td>
                                <td><span class="status-<?php echo esc_attr($r->status); ?>"><?php echo esc_html($statuses[$r->status] ?? $r->status); ?></span></td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 5px; min-width: 140px;">
                                        <?php if ($r->status === 'pending'): ?>
                                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=rr_update_reservation_status&id=' . $r->id . '&status=confirmed'), 'update_status_' . $r->id); ?>" class="button button-small button-primary" style="margin: 0; text-align: center;">Confirmar</a>
                                        <?php endif; ?>
                                        
                                        <?php if ($r->status === 'confirmed'): ?>
                                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=rr_update_reservation_status&id=' . $r->id . '&status=completed'), 'update_status_' . $r->id); ?>" class="button button-small" style="margin: 0; background: #46b450; color: white; border-color: #46b450; text-align: center;">Completar</a>
                                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=rr_update_reservation_status&id=' . $r->id . '&status=no-show'), 'update_status_' . $r->id); ?>" class="button button-small" style="margin: 0; background: #dc3232; color: white; border-color: #dc3232; text-align: center;">No se presentó</a>
                                        <?php endif; ?>
                                        
                                        <?php if ($r->status !== 'cancelled' && $r->status !== 'completed' && $r->status !== 'no-show'): ?>
                                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=rr_update_reservation_status&id=' . $r->id . '&status=cancelled'), 'update_status_' . $r->id); ?>" class="button button-small" style="margin: 0; text-align: center;">Cancelar</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    /**
     * Manejar actualización de estado
     */
    public function handle_update_status() {
        if (!current_user_can('manage_options')) {
            wp_die('No autorizado');
        }
        
        $id = absint($_GET['id']);
        $status = sanitize_text_field($_GET['status']);
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'update_status_' . $id)) {
            wp_die('Acción no autorizada');
        }
        
        RR_Reservations_Data::update_status($id, $status);
        
        // Enviar email según el estado
        if ($status === 'confirmed') {
            RR_Email::send_confirmation($id);
        } elseif ($status === 'cancelled') {
            RR_Email::send_cancellation($id);
        }
        
        wp_redirect(admin_url('admin.php?page=restaurant-reservations-reservations&message=updated'));
        exit;
    }
}
