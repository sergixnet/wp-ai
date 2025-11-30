<?php
/**
 * Plugin Name: Mailpit Configuration
 * Description: Configura WordPress para enviar emails a través de Mailpit en desarrollo
 * Version: 1.0.0
 * Author: Development Team
 */

if (!defined('ABSPATH')) exit;

/**
 * Configurar PHPMailer para usar Mailpit
 */
add_action('phpmailer_init', function($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'mailpit';
    $phpmailer->Port = 1025;
    $phpmailer->SMTPAuth = false;
    $phpmailer->SMTPSecure = false;
    $phpmailer->From = 'wordpress@wp-ai.dev';
    $phpmailer->FromName = 'WordPress WP-AI';
});

/**
 * Añadir enlace rápido a Mailpit en la barra de administración
 */
add_action('admin_bar_menu', function($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $wp_admin_bar->add_node(array(
        'id' => 'mailpit',
        'title' => '📧 Mailpit',
        'href' => 'http://localhost:8025',
        'meta' => array(
            'target' => '_blank',
            'title' => 'Ver emails en Mailpit'
        )
    ));
}, 100);
