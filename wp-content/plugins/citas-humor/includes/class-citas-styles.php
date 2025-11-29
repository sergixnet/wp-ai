<?php
/**
 * Clase para gestionar los estilos/temas
 */

if (!defined('ABSPATH')) {
    exit;
}

class Citas_Humor_Styles {
    
    /**
     * Obtener todos los temas disponibles
     */
    public static function get_themes() {
        return array(
            'gradient' => array(
                'name' => 'Gradiente Moderno',
                'description' => 'Diseño vibrante con gradiente morado'
            ),
            'classic' => array(
                'name' => 'Clásico',
                'description' => 'Estilo elegante y profesional'
            ),
            'minimal' => array(
                'name' => 'Minimalista',
                'description' => 'Diseño limpio y simple'
            ),
            'dark' => array(
                'name' => 'Oscuro',
                'description' => 'Tema oscuro para mejor lectura'
            ),
            'retro' => array(
                'name' => 'Retro',
                'description' => 'Estilo vintage años 80'
            )
        );
    }
    
    /**
     * Obtener CSS para un tema específico
     */
    public static function get_theme_css($theme) {
        $styles = array(
            'gradient' => "
                .cita-humor-box {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    padding: 25px 30px;
                    border-radius: 12px;
                    margin: 20px 0;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                    border-left: 5px solid #ffd700;
                }
                .cita-humor-texto {
                    color: #ffffff;
                    font-size: 18px;
                    line-height: 1.6;
                    font-style: italic;
                    margin: 0;
                    text-align: center;
                    font-weight: 500;
                }
            ",
            'classic' => "
                .cita-humor-box {
                    background: #f8f9fa;
                    padding: 30px 40px;
                    border-radius: 4px;
                    margin: 20px 0;
                    border: 2px solid #dee2e6;
                    border-left: 6px solid #007bff;
                }
                .cita-humor-texto {
                    color: #212529;
                    font-size: 18px;
                    line-height: 1.8;
                    font-style: italic;
                    margin: 0;
                    text-align: left;
                    font-weight: 400;
                    font-family: Georgia, serif;
                }
            ",
            'minimal' => "
                .cita-humor-box {
                    background: transparent;
                    padding: 20px 0;
                    border-radius: 0;
                    margin: 20px 0;
                    border-top: 1px solid #e0e0e0;
                    border-bottom: 1px solid #e0e0e0;
                }
                .cita-humor-texto {
                    color: #333333;
                    font-size: 16px;
                    line-height: 1.7;
                    font-style: normal;
                    margin: 0;
                    text-align: center;
                    font-weight: 300;
                }
            ",
            'dark' => "
                .cita-humor-box {
                    background: #1a1a1a;
                    padding: 25px 30px;
                    border-radius: 8px;
                    margin: 20px 0;
                    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
                    border: 1px solid #333;
                }
                .cita-humor-texto {
                    color: #00ff88;
                    font-size: 18px;
                    line-height: 1.6;
                    font-style: italic;
                    margin: 0;
                    text-align: center;
                    font-weight: 400;
                    text-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
                }
            ",
            'retro' => "
                .cita-humor-box {
                    background: linear-gradient(45deg, #ff6b9d 0%, #c06c84 50%, #6c5b7b 100%);
                    padding: 25px 30px;
                    border-radius: 0;
                    margin: 20px 0;
                    box-shadow: 8px 8px 0 rgba(0, 0, 0, 0.2);
                    border: 4px solid #355c7d;
                }
                .cita-humor-texto {
                    color: #ffffff;
                    font-size: 20px;
                    line-height: 1.5;
                    font-style: normal;
                    margin: 0;
                    text-align: center;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                    font-family: 'Courier New', monospace;
                }
            "
        );
        
        $base_css = "
            .cita-humor-texto::before {
                content: '\"';
                font-size: 30px;
                margin-right: 5px;
                opacity: 0.7;
            }
            .cita-humor-texto::after {
                content: '\"';
                font-size: 30px;
                margin-left: 5px;
                opacity: 0.7;
            }
            @media (max-width: 768px) {
                .cita-humor-box {
                    padding: 20px !important;
                }
                .cita-humor-texto {
                    font-size: 16px !important;
                }
            }
        ";
        
        return isset($styles[$theme]) ? $styles[$theme] . $base_css : $styles['gradient'] . $base_css;
    }
}
