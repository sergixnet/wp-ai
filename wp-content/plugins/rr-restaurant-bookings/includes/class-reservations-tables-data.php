<?php
/**
 * Clase para gestionar datos de mesas en la base de datos
 */

if (!defined('ABSPATH')) exit;

class RR_Tables_Data {
    
    /**
     * Obtener nombre de la tabla
     */
    private static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'rr_tables';
    }
    
    /**
     * Crear una nueva mesa
     * 
     * @param string $name Nombre de la mesa
     * @param int $capacity Capacidad de personas
     * @param string $status Estado (active/inactive)
     * @return int|false ID de la mesa creada o false si falla
     */
    public static function create_table($name, $capacity, $status = 'active') {
        global $wpdb;
        
        $result = $wpdb->insert(
            self::get_table_name(),
            array(
                'name' => sanitize_text_field($name),
                'capacity' => absint($capacity),
                'status' => sanitize_text_field($status)
            ),
            array('%s', '%d', '%s')
        );
        
        if ($result === false) {
            error_log('Restaurant Reservations: Error al crear mesa - ' . $wpdb->last_error);
            return false;
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Actualizar una mesa existente
     * 
     * @param int $table_id ID de la mesa
     * @param string $name Nombre de la mesa
     * @param int $capacity Capacidad de personas
     * @param string $status Estado
     * @return bool True si se actualizó correctamente
     */
    public static function update_table($table_id, $name, $capacity, $status) {
        global $wpdb;
        
        $result = $wpdb->update(
            self::get_table_name(),
            array(
                'name' => sanitize_text_field($name),
                'capacity' => absint($capacity),
                'status' => sanitize_text_field($status)
            ),
            array('id' => absint($table_id)),
            array('%s', '%d', '%s'),
            array('%d')
        );
        
        if ($result === false) {
            error_log('Restaurant Reservations: Error al actualizar mesa - ' . $wpdb->last_error);
            return false;
        }
        
        return true;
    }
    
    /**
     * Eliminar una mesa
     * 
     * @param int $table_id ID de la mesa
     * @return bool True si se eliminó correctamente
     */
    public static function delete_table($table_id) {
        global $wpdb;
        
        // Verificar si hay reservas asociadas
        $reservations_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}rr_reservations WHERE table_id = %d AND status != 'cancelled'",
            absint($table_id)
        ));
        
        if ($reservations_count > 0) {
            error_log('Restaurant Reservations: No se puede eliminar mesa con reservas activas');
            return false;
        }
        
        $result = $wpdb->delete(
            self::get_table_name(),
            array('id' => absint($table_id)),
            array('%d')
        );
        
        if ($result === false) {
            error_log('Restaurant Reservations: Error al eliminar mesa - ' . $wpdb->last_error);
            return false;
        }
        
        return true;
    }
    
    /**
     * Obtener todas las mesas
     * 
     * @param string $status Filtrar por estado (null para todas)
     * @return array Array de objetos de mesas
     */
    public static function get_all_tables($status = null) {
        global $wpdb;
        
        $table_name = self::get_table_name();
        
        if ($status !== null) {
            $sql = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE status = %s ORDER BY name ASC",
                sanitize_text_field($status)
            );
        } else {
            $sql = "SELECT * FROM $table_name ORDER BY name ASC";
        }
        
        $tables = $wpdb->get_results($sql);
        
        return $tables ? $tables : array();
    }
    
    /**
     * Obtener una mesa por ID
     * 
     * @param int $table_id ID de la mesa
     * @return object|null Objeto de la mesa o null si no existe
     */
    public static function get_table_by_id($table_id) {
        global $wpdb;
        
        $table = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM " . self::get_table_name() . " WHERE id = %d",
            absint($table_id)
        ));
        
        return $table;
    }
    
    /**
     * Obtener mesas disponibles para una capacidad específica
     * 
     * @param int $party_size Número de personas
     * @return array Array de mesas disponibles
     */
    public static function get_tables_by_capacity($party_size) {
        global $wpdb;
        
        $tables = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM " . self::get_table_name() . " 
            WHERE capacity >= %d AND status = 'active' 
            ORDER BY capacity ASC",
            absint($party_size)
        ));
        
        return $tables ? $tables : array();
    }
    
    /**
     * Contar total de mesas
     * 
     * @param string $status Filtrar por estado (null para todas)
     * @return int Número total de mesas
     */
    public static function count_tables($status = null) {
        global $wpdb;
        
        $table_name = self::get_table_name();
        
        if ($status !== null) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE status = %s",
                sanitize_text_field($status)
            ));
        } else {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        }
        
        return (int) $count;
    }
    
    /**
     * Cambiar estado de una mesa
     * 
     * @param int $table_id ID de la mesa
     * @param string $status Nuevo estado
     * @return bool True si se actualizó correctamente
     */
    public static function update_status($table_id, $status) {
        global $wpdb;
        
        $result = $wpdb->update(
            self::get_table_name(),
            array('status' => sanitize_text_field($status)),
            array('id' => absint($table_id)),
            array('%s'),
            array('%d')
        );
        
        return $result !== false;
    }
}
