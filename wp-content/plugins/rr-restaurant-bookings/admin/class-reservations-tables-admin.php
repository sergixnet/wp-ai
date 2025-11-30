<?php
/**
 * Clase para la página de administración de mesas
 */

if (!defined('ABSPATH')) exit;

// Cargar WP_List_Table si no está disponible
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class RR_Tables_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_page'));
        add_action('admin_post_rr_add_table', array($this, 'handle_add_table'));
        add_action('admin_post_rr_edit_table', array($this, 'handle_edit_table'));
        add_action('admin_post_rr_delete_table', array($this, 'handle_delete_table'));
    }
    
    /**
     * Añadir página al menú de administración
     */
    public function add_menu_page() {
        add_menu_page(
            'Restaurant Reservations',
            'Reservas',
            'manage_options',
            'restaurant-reservations',
            array($this, 'render_main_page'),
            'dashicons-calendar-alt',
            30
        );
        
        add_submenu_page(
            'restaurant-reservations',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'restaurant-reservations',
            array($this, 'render_main_page')
        );
        
        add_submenu_page(
            'restaurant-reservations',
            'Gestión de Mesas',
            'Mesas',
            'manage_options',
            'restaurant-reservations-tables',
            array($this, 'render_tables_page')
        );
    }
    
    /**
     * Renderizar página principal (dashboard)
     */
    public function render_main_page() {
        // Obtener estadísticas
        $total_tables = RR_Tables_Data::count_tables();
        $active_tables = RR_Tables_Data::count_tables('active');
        
        $today = date('Y-m-d');
        $reservations_today = RR_Reservations_Data::get_reservations_by_date($today);
        $pending_count = RR_Reservations_Data::count_reservations(array('status' => 'pending'));
        $confirmed_today = count(array_filter($reservations_today, function($r) { return $r->status === 'confirmed'; }));
        
        // Próximas reservas (hoy y los próximos 7 días)
        $upcoming = RR_Reservations_Data::get_all_reservations(array(
            'date_from' => $today,
            'date_to' => date('Y-m-d', strtotime('+7 days')),
            'orderby' => 'reservation_date',
            'order' => 'ASC',
            'limit' => 10
        ));
        ?>
        <div class="wrap">
            <h1>Restaurant Reservations - Dashboard</h1>
            
            <div class="rr-dashboard-cards" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0;">
                <div class="rr-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px; color: #666; font-size: 14px;">Total Mesas</h3>
                    <p style="margin: 0; font-size: 32px; font-weight: bold; color: #2271b1;"><?php echo $total_tables; ?></p>
                    <p style="margin: 5px 0 0; color: #666; font-size: 12px;"><?php echo $active_tables; ?> activas</p>
                </div>
                
                <div class="rr-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px; color: #666; font-size: 14px;">Reservas Hoy</h3>
                    <p style="margin: 0; font-size: 32px; font-weight: bold; color: #00a32a;"><?php echo count($reservations_today); ?></p>
                    <p style="margin: 5px 0 0; color: #666; font-size: 12px;"><?php echo $confirmed_today; ?> confirmadas</p>
                </div>
                
                <div class="rr-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px; color: #666; font-size: 14px;">Pendientes</h3>
                    <p style="margin: 0; font-size: 32px; font-weight: bold; color: #f0ad4e;"><?php echo $pending_count; ?></p>
                    <p style="margin: 5px 0 0; color: #666; font-size: 12px;">Requieren atención</p>
                </div>
                
                <div class="rr-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px; color: #666; font-size: 14px;">Próximos 7 días</h3>
                    <p style="margin: 0; font-size: 32px; font-weight: bold; color: #5cb85c;"><?php echo count($upcoming); ?></p>
                    <p style="margin: 5px 0 0; color: #666; font-size: 12px;">Reservas futuras</p>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 30px;">
                <div class="rr-dashboard-section" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h2 style="margin-top: 0;">Próximas Reservas</h2>
                    
                    <?php if (empty($upcoming)): ?>
                        <p style="color: #666;">No hay reservas próximas.</p>
                    <?php else: ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Mesa</th>
                                    <th>Personas</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcoming as $r): ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($r->customer_name); ?></strong></td>
                                        <td><?php echo esc_html(date_i18n('d/m/Y', strtotime($r->reservation_date))); ?></td>
                                        <td><?php echo esc_html(date_i18n('H:i', strtotime($r->reservation_time))); ?></td>
                                        <td><?php echo esc_html($r->table_name); ?></td>
                                        <td><?php echo esc_html($r->party_size); ?></td>
                                        <td><span class="status-<?php echo esc_attr($r->status); ?>"><?php echo esc_html(ucfirst($r->status)); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    
                    <p style="margin-top: 15px;">
                        <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-reservations'); ?>" class="button button-primary">Ver todas las reservas</a>
                    </p>
                </div>
                
                <div class="rr-dashboard-section" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h2 style="margin-top: 0;">Accesos Rápidos</h2>
                    
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-reservations'); ?>" class="button button-large" style="display: block; text-align: left;">
                            <span class="dashicons dashicons-list-view" style="vertical-align: middle;"></span> Ver Reservas
                        </a>
                        
                        <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-calendar'); ?>" class="button button-large" style="display: block; text-align: left;">
                            <span class="dashicons dashicons-calendar-alt" style="vertical-align: middle;"></span> Calendario
                        </a>
                        
                        <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-tables'); ?>" class="button button-large" style="display: block; text-align: left;">
                            <span class="dashicons dashicons-editor-table" style="vertical-align: middle;"></span> Gestionar Mesas
                        </a>
                        
                        <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-settings'); ?>" class="button button-large" style="display: block; text-align: left;">
                            <span class="dashicons dashicons-admin-settings" style="vertical-align: middle;"></span> Configuración
                        </a>
                    </div>
                    
                    <hr style="margin: 20px 0;">
                    
                    <h3>Reservas de Hoy</h3>
                    <?php if (empty($reservations_today)): ?>
                        <p style="color: #666; font-size: 14px;">No hay reservas para hoy.</p>
                    <?php else: ?>
                        <ul style="margin: 0; padding-left: 20px;">
                            <?php foreach ($reservations_today as $r): ?>
                                <li style="margin: 8px 0;">
                                    <strong><?php echo esc_html(date_i18n('H:i', strtotime($r->reservation_time))); ?></strong> - 
                                    <?php echo esc_html($r->customer_name); ?> 
                                    <span style="color: #666;">(<?php echo esc_html($r->party_size); ?> pers.)</span>
                                    <br>
                                    <span class="status-<?php echo esc_attr($r->status); ?>" style="font-size: 12px;">
                                        <?php echo esc_html(ucfirst($r->status)); ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renderizar página de gestión de mesas
     */
    public function render_tables_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        
        switch ($action) {
            case 'add':
                $this->render_add_table_form();
                break;
            case 'edit':
                $this->render_edit_table_form();
                break;
            default:
                $this->render_tables_list();
                break;
        }
    }
    
    /**
     * Renderizar lista de mesas
     */
    private function render_tables_list() {
        // Mensajes de estado
        if (isset($_GET['message'])) {
            $message = sanitize_text_field($_GET['message']);
            $messages = array(
                'added' => 'Mesa añadida correctamente.',
                'updated' => 'Mesa actualizada correctamente.',
                'deleted' => 'Mesa eliminada correctamente.',
                'error' => 'Ha ocurrido un error. Por favor, inténtalo de nuevo.'
            );
            
            if (isset($messages[$message])) {
                $class = $message === 'error' ? 'error' : 'updated';
                echo '<div class="notice notice-' . $class . ' is-dismissible"><p>' . esc_html($messages[$message]) . '</p></div>';
            }
        }
        
        $tables = RR_Tables_Data::get_all_tables();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Gestión de Mesas</h1>
            <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-tables&action=add'); ?>" class="page-title-action">Añadir nueva</a>
            <hr class="wp-heading-inline">
            
            <?php if (empty($tables)): ?>
                <p>No hay mesas creadas todavía.</p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                            <th>Fecha de creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tables as $table): ?>
                            <tr>
                                <td><?php echo esc_html($table->id); ?></td>
                                <td><strong><?php echo esc_html($table->name); ?></strong></td>
                                <td><?php echo esc_html($table->capacity); ?> personas</td>
                                <td>
                                    <?php if ($table->status === 'active'): ?>
                                        <span class="dashicons dashicons-yes-alt" style="color: green;" title="Activa"></span> Activa
                                    <?php else: ?>
                                        <span class="dashicons dashicons-dismiss" style="color: red;" title="Inactiva"></span> Inactiva
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($table->created_at))); ?></td>
                                <td>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=restaurant-reservations-tables&action=edit&table_id=' . $table->id), 'edit_table_' . $table->id); ?>" class="button button-small">Editar</a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=rr_delete_table&table_id=' . $table->id), 'delete_table_' . $table->id); ?>" class="button button-small button-link-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar esta mesa?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
            <br>
            <p class="description">
                <strong>Estadísticas:</strong> Total de mesas: <?php echo count($tables); ?> | 
                Activas: <?php echo count(array_filter($tables, function($t) { return $t->status === 'active'; })); ?> | 
                Inactivas: <?php echo count(array_filter($tables, function($t) { return $t->status === 'inactive'; })); ?>
            </p>
        </div>
        <?php
    }
    
    /**
     * Renderizar formulario para añadir mesa
     */
    private function render_add_table_form() {
        ?>
        <div class="wrap">
            <h1>Añadir Nueva Mesa</h1>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('rr_add_table', 'rr_add_table_nonce'); ?>
                <input type="hidden" name="action" value="rr_add_table">
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="table_name">Nombre de la Mesa *</label></th>
                        <td>
                            <input type="text" name="table_name" id="table_name" class="regular-text" required>
                            <p class="description">Ej: Mesa 1, Mesa VIP, Terraza 5</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="table_capacity">Capacidad *</label></th>
                        <td>
                            <input type="number" name="table_capacity" id="table_capacity" min="1" max="50" value="2" required>
                            <p class="description">Número máximo de personas</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="table_status">Estado</label></th>
                        <td>
                            <select name="table_status" id="table_status">
                                <option value="active">Activa</option>
                                <option value="inactive">Inactiva</option>
                            </select>
                            <p class="description">Las mesas inactivas no aparecerán disponibles para reservas</p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" class="button button-primary" value="Añadir Mesa">
                    <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-tables'); ?>" class="button">Cancelar</a>
                </p>
            </form>
        </div>
        <?php
    }
    
    /**
     * Renderizar formulario para editar mesa
     */
    private function render_edit_table_form() {
        $table_id = isset($_GET['table_id']) ? absint($_GET['table_id']) : 0;
        
        // Verificar nonce
        if (!wp_verify_nonce($_GET['_wpnonce'], 'edit_table_' . $table_id)) {
            wp_die('Acción no autorizada');
        }
        
        $table = RR_Tables_Data::get_table_by_id($table_id);
        
        if (!$table) {
            wp_die('Mesa no encontrada');
        }
        ?>
        <div class="wrap">
            <h1>Editar Mesa</h1>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('rr_edit_table_' . $table_id, 'rr_edit_table_nonce'); ?>
                <input type="hidden" name="action" value="rr_edit_table">
                <input type="hidden" name="table_id" value="<?php echo esc_attr($table_id); ?>">
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="table_name">Nombre de la Mesa *</label></th>
                        <td>
                            <input type="text" name="table_name" id="table_name" class="regular-text" value="<?php echo esc_attr($table->name); ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="table_capacity">Capacidad *</label></th>
                        <td>
                            <input type="number" name="table_capacity" id="table_capacity" min="1" max="50" value="<?php echo esc_attr($table->capacity); ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="table_status">Estado</label></th>
                        <td>
                            <select name="table_status" id="table_status">
                                <option value="active" <?php selected($table->status, 'active'); ?>>Activa</option>
                                <option value="inactive" <?php selected($table->status, 'inactive'); ?>>Inactiva</option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" class="button button-primary" value="Actualizar Mesa">
                    <a href="<?php echo admin_url('admin.php?page=restaurant-reservations-tables'); ?>" class="button">Cancelar</a>
                </p>
            </form>
        </div>
        <?php
    }
    
    /**
     * Manejar añadir mesa
     */
    public function handle_add_table() {
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos para realizar esta acción');
        }
        
        // Verificar nonce
        if (!isset($_POST['rr_add_table_nonce']) || !wp_verify_nonce($_POST['rr_add_table_nonce'], 'rr_add_table')) {
            wp_die('Acción no autorizada');
        }
        
        $name = sanitize_text_field($_POST['table_name']);
        $capacity = absint($_POST['table_capacity']);
        $status = sanitize_text_field($_POST['table_status']);
        
        $result = RR_Tables_Data::create_table($name, $capacity, $status);
        
        if ($result) {
            wp_redirect(admin_url('admin.php?page=restaurant-reservations-tables&message=added'));
        } else {
            wp_redirect(admin_url('admin.php?page=restaurant-reservations-tables&message=error'));
        }
        exit;
    }
    
    /**
     * Manejar editar mesa
     */
    public function handle_edit_table() {
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos para realizar esta acción');
        }
        
        $table_id = absint($_POST['table_id']);
        
        // Verificar nonce
        if (!isset($_POST['rr_edit_table_nonce']) || !wp_verify_nonce($_POST['rr_edit_table_nonce'], 'rr_edit_table_' . $table_id)) {
            wp_die('Acción no autorizada');
        }
        
        $name = sanitize_text_field($_POST['table_name']);
        $capacity = absint($_POST['table_capacity']);
        $status = sanitize_text_field($_POST['table_status']);
        
        $result = RR_Tables_Data::update_table($table_id, $name, $capacity, $status);
        
        if ($result) {
            wp_redirect(admin_url('admin.php?page=restaurant-reservations-tables&message=updated'));
        } else {
            wp_redirect(admin_url('admin.php?page=restaurant-reservations-tables&message=error'));
        }
        exit;
    }
    
    /**
     * Manejar eliminar mesa
     */
    public function handle_delete_table() {
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos para realizar esta acción');
        }
        
        $table_id = absint($_GET['table_id']);
        
        // Verificar nonce
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_table_' . $table_id)) {
            wp_die('Acción no autorizada');
        }
        
        $result = RR_Tables_Data::delete_table($table_id);
        
        if ($result) {
            wp_redirect(admin_url('admin.php?page=restaurant-reservations-tables&message=deleted'));
        } else {
            wp_redirect(admin_url('admin.php?page=restaurant-reservations-tables&message=error'));
        }
        exit;
    }
}
