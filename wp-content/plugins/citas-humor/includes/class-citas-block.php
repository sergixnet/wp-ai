<?php
/**
 * Clase para gestionar el bloque de Gutenberg
 */

if (!defined('ABSPATH')) {
    exit;
}

class Citas_Humor_Block {
    
    public function __construct() {
        add_action('init', array($this, 'register_block'));
    }
    
    /**
     * Registrar el bloque de Gutenberg
     */
    public function register_block() {
        // Verificar si existe el archivo compilado
        $asset_file = CITAS_HUMOR_PATH . 'build/index.asset.php';
        
        if (!file_exists($asset_file)) {
            return;
        }
        
        $asset = require $asset_file;
        
        // Registrar el script del bloque
        wp_register_script(
            'citas-humor-block',
            CITAS_HUMOR_URL . 'build/index.js',
            $asset['dependencies'],
            $asset['version']
        );
        
        // Registrar estilos del editor
        wp_register_style(
            'citas-humor-block-editor',
            CITAS_HUMOR_URL . 'build/index.css',
            array(),
            $asset['version']
        );
        
        // Registrar estilos del frontend
        wp_register_style(
            'citas-humor-block-style',
            CITAS_HUMOR_URL . 'build/style-index.css',
            array(),
            $asset['version']
        );
        
        // Registrar el bloque
        register_block_type('citas-humor/cita-block', array(
            'editor_script' => 'citas-humor-block',
            'editor_style' => 'citas-humor-block-editor',
            'style' => 'citas-humor-block-style',
            'render_callback' => array($this, 'render_block')
        ));
    }
    
    /**
     * Renderizar el bloque en el frontend
     */
    public function render_block($attributes, $content) {
        $clase = isset($attributes['className']) ? esc_attr($attributes['className']) : 'cita-humor-box';
        $theme = isset($attributes['theme']) ? esc_attr($attributes['theme']) : 'current';
        $cita = Citas_Humor_Data::get_random_cita();
        
        // Si el tema es 'current', usar el tema global del plugin
        if ($theme === 'current') {
            $theme = get_option('citas_humor_theme', 'gradient');
        }
        
        // Cargar estilos del tema seleccionado
        $css = Citas_Humor_Styles::get_theme_css($theme);
        
        $output = '<style>' . $css . '</style>';
        $output .= '<div class="' . $clase . '">';
        $output .= '<p class="cita-humor-texto">' . esc_html($cita) . '</p>';
        $output .= '</div>';
        
        return $output;
    }
}
