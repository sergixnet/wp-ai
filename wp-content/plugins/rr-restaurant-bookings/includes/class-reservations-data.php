<?php
/**
 * Clase para gestionar datos de reservas en la base de datos
 */

if (!defined('ABSPATH')) exit;

class RR_Reservations_Data {
    
    /**
     * Obtener nombre de la tabla
     */
    private static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'rr_reservations';
    }
    
    /**
     * Crear una nueva reserva
     * 
     * @param array $data Datos de la reserva
     * @return int|false ID de la reserva creada o false si falla
     */
    public static function create_reservation($data) {
        global $wpdb;
        
        // Validar disponibilidad antes de crear
        $is_available = self::check_availability(
            $data['table_id'],
            $data['reservation_date'],
            $data['reservation_time']
        );
        
        if (!$is_available) {
            error_log('Restaurant Reservations: Mesa no disponible en esa fecha/hora');
            return false;
        }
        
        $result = $wpdb->insert(
            self::get_table_name(),
            array(
                'table_id' => absint($data['table_id']),
                'customer_name' => sanitize_text_field($data['customer_name']),
                'customer_email' => sanitize_email($data['customer_email']),
                'customer_phone' => sanitize_text_field($data['customer_phone']),
                'reservation_date' => sanitize_text_field($data['reservation_date']),
                'reservation_time' => sanitize_text_field($data['reservation_time']),
                'party_size' => absint($data['party_size']),
                'status' => isset($data['status']) ? sanitize_text_field($data['status']) : 'pending',
                'special_requests' => isset($data['special_requests']) ? sanitize_textarea_field($data['special_requests']) : ''
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
        );
        
        if ($result === false) {
            error_log('Restaurant Reservations: Error al crear reserva - ' . $wpdb->last_error);
            return false;
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Actualizar estado de una reserva
     * 
     * @param int $reservation_id ID de la reserva
     * @param string $status Nuevo estado
     * @return bool True si se actualizó correctamente
     */
    public static function update_status($reservation_id, $status) {
        global $wpdb;
        
        $valid_statuses = array('pending', 'confirmed', 'cancelled', 'completed', 'no-show');
        
        if (!in_array($status, $valid_statuses)) {
            error_log('Restaurant Reservations: Estado no válido - ' . $status);
            return false;
        }
        
        $result = $wpdb->update(
            self::get_table_name(),
            array('status' => sanitize_text_field($status)),
            array('id' => absint($reservation_id)),
            array('%s'),
            array('%d')
        );
        
        return $result !== false;
    }
    
    /**
     * Obtener reservas por fecha
     * 
     * @param string $date Fecha en formato Y-m-d
     * @param string $status Filtrar por estado (null para todas)
     * @return array Array de objetos de reservas
     */
    public static function get_reservations_by_date($date, $status = null) {
        global $wpdb;
        
        $table_name = self::get_table_name();
        $tables_name = $wpdb->prefix . 'rr_tables';
        
        if ($status !== null) {
            $sql = $wpdb->prepare(
                "SELECT r.*, t.name as table_name, t.capacity as table_capacity 
                FROM $table_name r 
                LEFT JOIN $tables_name t ON r.table_id = t.id 
                WHERE r.reservation_date = %s AND r.status = %s 
                ORDER BY r.reservation_time ASC",
                sanitize_text_field($date),
                sanitize_text_field($status)
            );
        } else {
            $sql = $wpdb->prepare(
                "SELECT r.*, t.name as table_name, t.capacity as table_capacity 
                FROM $table_name r 
                LEFT JOIN $tables_name t ON r.table_id = t.id 
                WHERE r.reservation_date = %s 
                ORDER BY r.reservation_time ASC",
                sanitize_text_field($date)
            );
        }
        
        $reservations = $wpdb->get_results($sql);
        
        return $reservations ? $reservations : array();
    }
    
    /**
     * Obtener una reserva por ID
     * 
     * @param int $reservation_id ID de la reserva
     * @return object|null Objeto de la reserva o null si no existe
     */
    public static function get_reservation_by_id($reservation_id) {
        global $wpdb;
        
        $table_name = self::get_table_name();
        $tables_name = $wpdb->prefix . 'rr_tables';
        
        $reservation = $wpdb->get_row($wpdb->prepare(
            "SELECT r.*, t.name as table_name, t.capacity as table_capacity 
            FROM $table_name r 
            LEFT JOIN $tables_name t ON r.table_id = t.id 
            WHERE r.id = %d",
            absint($reservation_id)
        ));
        
        return $reservation;
    }
    
    /**
     * Verificar disponibilidad de una mesa
     * 
     * @param int $table_id ID de la mesa
     * @param string $date Fecha en formato Y-m-d
     * @param string $time Hora en formato H:i
     * @return bool True si está disponible
     */
    public static function check_availability($table_id, $date, $time) {
        global $wpdb;
        
        // Obtener duración de reserva configurada (en minutos)
        $duration = absint(get_option('rr_reservation_duration', 120));
        
        // Calcular rango de tiempo que ocuparía la reserva
        $start_time = strtotime($time);
        $end_time = $start_time + ($duration * 60);
        
        // Buscar reservas conflictivas
        $table_name = self::get_table_name();
        
        $conflicts = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
            WHERE table_id = %d 
            AND reservation_date = %s 
            AND status IN ('pending', 'confirmed')
            AND (
                (TIME_TO_SEC(reservation_time) <= %d AND TIME_TO_SEC(reservation_time) + (%d * 60) > %d)
                OR (TIME_TO_SEC(reservation_time) < %d AND TIME_TO_SEC(reservation_time) >= %d)
            )",
            absint($table_id),
            sanitize_text_field($date),
            $start_time - strtotime('00:00:00'),
            $duration,
            $start_time - strtotime('00:00:00'),
            $end_time - strtotime('00:00:00'),
            $start_time - strtotime('00:00:00')
        ));
        
        return $conflicts == 0;
    }
    
    /**
     * Obtener todas las reservas con filtros
     * 
     * @param array $args Argumentos de filtrado
     * @return array Array de objetos de reservas
     */
    public static function get_all_reservations($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => null,
            'date_from' => null,
            'date_to' => null,
            'table_id' => null,
            'orderby' => 'reservation_date',
            'order' => 'DESC',
            'limit' => null,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $table_name = self::get_table_name();
        $tables_name = $wpdb->prefix . 'rr_tables';
        
        $where = array('1=1');
        
        if ($args['status']) {
            $where[] = $wpdb->prepare('r.status = %s', sanitize_text_field($args['status']));
        }
        
        if ($args['date_from']) {
            $where[] = $wpdb->prepare('r.reservation_date >= %s', sanitize_text_field($args['date_from']));
        }
        
        if ($args['date_to']) {
            $where[] = $wpdb->prepare('r.reservation_date <= %s', sanitize_text_field($args['date_to']));
        }
        
        if ($args['table_id']) {
            $where[] = $wpdb->prepare('r.table_id = %d', absint($args['table_id']));
        }
        
        $where_clause = implode(' AND ', $where);
        
        $orderby = sanitize_text_field($args['orderby']);
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
        
        $sql = "SELECT r.*, t.name as table_name, t.capacity as table_capacity 
                FROM $table_name r 
                LEFT JOIN $tables_name t ON r.table_id = t.id 
                WHERE $where_clause 
                ORDER BY r.$orderby $order";
        
        if ($args['limit']) {
            $sql .= $wpdb->prepare(' LIMIT %d OFFSET %d', absint($args['limit']), absint($args['offset']));
        }
        
        $reservations = $wpdb->get_results($sql);
        
        return $reservations ? $reservations : array();
    }
    
    /**
     * Contar total de reservas con filtros
     * 
     * @param array $args Argumentos de filtrado
     * @return int Número total de reservas
     */
    public static function count_reservations($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => null,
            'date_from' => null,
            'date_to' => null,
            'table_id' => null
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $table_name = self::get_table_name();
        $where = array('1=1');
        
        if ($args['status']) {
            $where[] = $wpdb->prepare('status = %s', sanitize_text_field($args['status']));
        }
        
        if ($args['date_from']) {
            $where[] = $wpdb->prepare('reservation_date >= %s', sanitize_text_field($args['date_from']));
        }
        
        if ($args['date_to']) {
            $where[] = $wpdb->prepare('reservation_date <= %s', sanitize_text_field($args['date_to']));
        }
        
        if ($args['table_id']) {
            $where[] = $wpdb->prepare('table_id = %d', absint($args['table_id']));
        }
        
        $where_clause = implode(' AND ', $where);
        
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE $where_clause");
        
        return (int) $count;
    }
    
    /**
     * Eliminar una reserva
     * 
     * @param int $reservation_id ID de la reserva
     * @return bool True si se eliminó correctamente
     */
    public static function delete_reservation($reservation_id) {
        global $wpdb;
        
        $result = $wpdb->delete(
            self::get_table_name(),
            array('id' => absint($reservation_id)),
            array('%d')
        );
        
        if ($result === false) {
            error_log('Restaurant Reservations: Error al eliminar reserva - ' . $wpdb->last_error);
            return false;
        }
        
        return true;
    }
    
    /**
     * Obtener franjas horarias disponibles para una fecha
     * 
     * @param string $date Fecha en formato Y-m-d
     * @param int $party_size Número de personas
     * @return array Array de horarios disponibles
     */
    public static function get_available_time_slots($date, $party_size) {
        // Obtener configuración
        $opening_time = get_option('rr_opening_time', '12:00');
        $closing_time = get_option('rr_closing_time', '23:00');
        $interval = absint(get_option('rr_time_slot_interval', 30));
        $min_advance_hours = absint(get_option('rr_min_advance_hours', 2));
        
        // Verificar si la fecha es hoy y aplicar tiempo mínimo de antelación
        $now = current_time('timestamp');
        $min_datetime = $now + ($min_advance_hours * 3600);
        $is_today = date('Y-m-d', $now) === $date;
        
        // Generar franjas horarias
        $start = strtotime($opening_time);
        $end = strtotime($closing_time);
        $slots = array();
        
        // Obtener mesas disponibles para el tamaño del grupo
        $available_tables = RR_Tables_Data::get_tables_by_capacity($party_size);
        
        if (empty($available_tables)) {
            return array(); // No hay mesas con suficiente capacidad
        }
        
        $current = $start;
        while ($current < $end) {
            $time = date('H:i', $current);
            $slot_datetime = strtotime($date . ' ' . $time);
            
            // Verificar tiempo mínimo de antelación
            if ($is_today && $slot_datetime < $min_datetime) {
                $current += ($interval * 60);
                continue;
            }
            
            // Verificar si al menos una mesa está disponible en este horario
            $has_available_table = false;
            foreach ($available_tables as $table) {
                if (self::check_availability($table->id, $date, $time)) {
                    $has_available_table = true;
                    break;
                }
            }
            
            if ($has_available_table) {
                $slots[] = $time;
            }
            
            $current += ($interval * 60);
        }
        
        return $slots;
    }
}
