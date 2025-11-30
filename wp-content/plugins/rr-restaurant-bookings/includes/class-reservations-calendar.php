<?php
/**
 * Clase para lógica de calendario y disponibilidad
 */

if (!defined('ABSPATH')) exit;

class RR_Calendar {
    
    /**
     * Obtener reservas para un rango de fechas (para vista de calendario)
     * 
     * @param string $start_date Fecha inicio (Y-m-d)
     * @param string $end_date Fecha fin (Y-m-d)
     * @return array Array de reservas agrupadas por fecha
     */
    public static function get_reservations_for_range($start_date, $end_date) {
        $reservations = RR_Reservations_Data::get_all_reservations(array(
            'date_from' => $start_date,
            'date_to' => $end_date,
            'orderby' => 'reservation_date',
            'order' => 'ASC'
        ));
        
        // Agrupar por fecha
        $grouped = array();
        foreach ($reservations as $reservation) {
            $date = $reservation->reservation_date;
            if (!isset($grouped[$date])) {
                $grouped[$date] = array();
            }
            $grouped[$date][] = $reservation;
        }
        
        return $grouped;
    }
    
    /**
     * Verificar si una fecha está cerrada
     * 
     * @param string $date Fecha (Y-m-d)
     * @return bool True si está cerrado
     */
    public static function is_closed_date($date) {
        $closed_days = get_option('rr_closed_days', array());
        
        // Asegurar que sea un array
        if (!is_array($closed_days)) {
            $closed_days = array();
        }
        
        $day_of_week = date('w', strtotime($date));
        
        return in_array($day_of_week, $closed_days);
    }
}
