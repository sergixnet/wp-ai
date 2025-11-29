<?php
/**
 * Clase para gestionar el shortcode
 */

if (!defined('ABSPATH')) {
    exit;
}

class Citas_Humor_Shortcode {
    
    public function __construct() {
        add_shortcode('cita_humor', array($this, 'render_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }
    
    /**
     * Renderizar el shortcode
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'clase' => 'cita-humor-box',
        ), $atts, 'cita_humor');
        
        $cita = Citas_Humor_Data::get_random_cita();
        
        $output = '<div class="' . esc_attr($atts['clase']) . '">';
        $output .= '<p class="cita-humor-texto">' . esc_html($cita) . '</p>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Cargar estilos CSS
     */
    public function enqueue_styles() {
        $theme = get_option('citas_humor_theme', 'gradient');
        $css = Citas_Humor_Styles::get_theme_css($theme);
        wp_add_inline_style('wp-block-library', $css);
    }
}
