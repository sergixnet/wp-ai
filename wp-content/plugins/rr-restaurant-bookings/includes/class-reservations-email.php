<?php
/**
 * Clase para la gestión de emails de reservas
 */

if (!defined('ABSPATH')) exit;

class RR_Email {
    
    /**
     * Enviar email de confirmación de reserva
     * 
     * @param int $reservation_id ID de la reserva
     * @return bool True si se envió correctamente
     */
    public static function send_confirmation($reservation_id) {
        $reservation = RR_Reservations_Data::get_reservation_by_id($reservation_id);
        
        if (!$reservation) {
            return false;
        }
        
        $to = $reservation->customer_email;
        $subject = sprintf('Confirmación de reserva - %s', get_bloginfo('name'));
        
        $message = self::get_confirmation_template($reservation);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('rr_admin_email') . '>'
        );
        
        $result = wp_mail($to, $subject, $message, $headers);
        
        // Enviar copia al administrador
        $admin_email = get_option('rr_admin_email');
        if ($admin_email) {
            wp_mail($admin_email, 'Nueva reserva - ' . $reservation->customer_name, $message, $headers);
        }
        
        return $result;
    }
    
    /**
     * Enviar email de cancelación
     * 
     * @param int $reservation_id ID de la reserva
     * @return bool True si se envió correctamente
     */
    public static function send_cancellation($reservation_id) {
        $reservation = RR_Reservations_Data::get_reservation_by_id($reservation_id);
        
        if (!$reservation) {
            return false;
        }
        
        $to = $reservation->customer_email;
        $subject = sprintf('Cancelación de reserva - %s', get_bloginfo('name'));
        
        $message = self::get_cancellation_template($reservation);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('rr_admin_email') . '>'
        );
        
        return wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * Plantilla HTML para email de confirmación
     */
    private static function get_confirmation_template($reservation) {
        $date_formatted = date_i18n(get_option('date_format'), strtotime($reservation->reservation_date));
        $time_formatted = date_i18n(get_option('time_format'), strtotime($reservation->reservation_time));
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .details { background: white; padding: 15px; margin: 20px 0; border-left: 4px solid #4CAF50; }
                .details strong { display: inline-block; width: 150px; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Reserva Confirmada</h1>
                </div>
                <div class="content">
                    <p>Hola <strong><?php echo esc_html($reservation->customer_name); ?></strong>,</p>
                    <p>Tu reserva ha sido <?php echo $reservation->status === 'confirmed' ? 'confirmada' : 'recibida'; ?> correctamente.</p>
                    
                    <div class="details">
                        <p><strong>Fecha:</strong> <?php echo esc_html($date_formatted); ?></p>
                        <p><strong>Hora:</strong> <?php echo esc_html($time_formatted); ?></p>
                        <p><strong>Mesa:</strong> <?php echo esc_html($reservation->table_name); ?></p>
                        <p><strong>Comensales:</strong> <?php echo esc_html($reservation->party_size); ?> persona<?php echo $reservation->party_size > 1 ? 's' : ''; ?></p>
                        <?php if ($reservation->special_requests): ?>
                            <p><strong>Peticiones especiales:</strong><br><?php echo nl2br(esc_html($reservation->special_requests)); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($reservation->status === 'pending'): ?>
                        <p><em>Tu reserva está pendiente de confirmación. Te avisaremos cuando sea confirmada.</em></p>
                    <?php endif; ?>
                    
                    <p>Te esperamos. Si necesitas cancelar o modificar tu reserva, por favor contáctanos.</p>
                </div>
                <div class="footer">
                    <p><?php echo get_bloginfo('name'); ?><br>
                    <?php echo get_option('rr_admin_email'); ?></p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Plantilla HTML para email de cancelación
     */
    private static function get_cancellation_template($reservation) {
        $date_formatted = date_i18n(get_option('date_format'), strtotime($reservation->reservation_date));
        $time_formatted = date_i18n(get_option('time_format'), strtotime($reservation->reservation_time));
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #f44336; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .details { background: white; padding: 15px; margin: 20px 0; border-left: 4px solid #f44336; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>❌ Reserva Cancelada</h1>
                </div>
                <div class="content">
                    <p>Hola <strong><?php echo esc_html($reservation->customer_name); ?></strong>,</p>
                    <p>Tu reserva ha sido cancelada.</p>
                    
                    <div class="details">
                        <p><strong>Fecha:</strong> <?php echo esc_html($date_formatted); ?></p>
                        <p><strong>Hora:</strong> <?php echo esc_html($time_formatted); ?></p>
                        <p><strong>Mesa:</strong> <?php echo esc_html($reservation->table_name); ?></p>
                    </div>
                    
                    <p>Esperamos verte pronto. Puedes hacer una nueva reserva cuando lo desees.</p>
                </div>
                <div class="footer">
                    <p><?php echo get_bloginfo('name'); ?><br>
                    <?php echo get_option('rr_admin_email'); ?></p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
