<?php
/**
 * Clase para la página de administración del calendario
 */

if (!defined('ABSPATH')) exit;

class RR_Calendar_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_page'));
    }
    
    /**
     * Añadir página al menú
     */
    public function add_menu_page() {
        add_submenu_page(
            'restaurant-reservations',
            'Calendario de Reservas',
            'Calendario',
            'manage_options',
            'restaurant-reservations-calendar',
            array($this, 'render_calendar_page')
        );
    }
    
    /**
     * Renderizar página de calendario
     */
    public function render_calendar_page() {
        $current_month = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : date('Y-m');
        $start_date = $current_month . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $reservations = RR_Calendar::get_reservations_for_range($start_date, $end_date);
        
        $prev_month = date('Y-m', strtotime($start_date . ' -1 month'));
        $next_month = date('Y-m', strtotime($start_date . ' +1 month'));
        ?>
        <div class="wrap">
            <h1>Calendario de Reservas</h1>
            
            <div style="margin: 20px 0;">
                <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-calendar&month=' . $prev_month); ?>" class="button">← Mes anterior</a>
                <strong style="margin: 0 20px;"><?php echo date_i18n('F Y', strtotime($start_date)); ?></strong>
                <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-calendar&month=' . $next_month); ?>" class="button">Mes siguiente →</a>
            </div>
            
            <table class="wp-list-table widefat fixed">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Total Reservas</th>
                        <th>Pendientes</th>
                        <th>Confirmadas</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $current_date = $start_date;
                    while ($current_date <= $end_date) {
                        $day_reservations = isset($reservations[$current_date]) ? $reservations[$current_date] : array();
                        $pending = count(array_filter($day_reservations, function($r) { return $r->status === 'pending'; }));
                        $confirmed = count(array_filter($day_reservations, function($r) { return $r->status === 'confirmed'; }));
                        ?>
                        <tr>
                            <td><strong><?php echo date_i18n('l, j F Y', strtotime($current_date)); ?></strong></td>
                            <td><?php echo count($day_reservations); ?></td>
                            <td><?php echo $pending; ?></td>
                            <td><?php echo $confirmed; ?></td>
                            <td>
                                <?php if (count($day_reservations) > 0): ?>
                                    <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-reservations&date=' . $current_date); ?>" class="button button-small">Ver detalles</a>
                                <?php else: ?>
                                    <span class="description">Sin reservas</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                        $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
