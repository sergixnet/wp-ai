<?php
/**
 * Clase para gestionar el panel de administración
 */

if (!defined('ABSPATH')) {
    exit;
}

class Citas_Humor_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Agregar página al menú de administración
     */
    public function add_menu_page() {
        add_options_page(
            'Ajustes de Citas de Humor',
            'Citas de Humor',
            'manage_options',
            'citas-humor-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Registrar configuraciones
     */
    public function register_settings() {
        register_setting('citas_humor_settings', 'citas_humor_theme');
        
        add_settings_section(
            'citas_humor_main_section',
            'Configuración de Temas',
            array($this, 'render_section_description'),
            'citas-humor-settings'
        );
        
        add_settings_field(
            'citas_humor_theme_field',
            'Seleccionar Tema',
            array($this, 'render_theme_field'),
            'citas-humor-settings',
            'citas_humor_main_section'
        );
    }
    
    /**
     * Descripción de la sección
     */
    public function render_section_description() {
        echo '<p>Selecciona el tema visual que deseas usar para mostrar las citas en tu sitio.</p>';
    }
    
    /**
     * Renderizar campo de selección de tema
     */
    public function render_theme_field() {
        $current_theme = get_option('citas_humor_theme', 'gradient');
        $themes = Citas_Humor_Styles::get_themes();
        
        echo '<select name="citas_humor_theme" id="citas_humor_theme">';
        foreach ($themes as $key => $theme) {
            $selected = ($current_theme === $key) ? 'selected' : '';
            echo '<option value="' . esc_attr($key) . '" ' . $selected . '>';
            echo esc_html($theme['name']) . ' - ' . esc_html($theme['description']);
            echo '</option>';
        }
        echo '</select>';
        
        // Vista previa
        echo '<div style="margin-top: 20px;">';
        echo '<h3>Vista Previa:</h3>';
        echo '<div id="theme-preview" style="max-width: 600px;">';
        $this->render_preview($current_theme);
        echo '</div>';
        echo '</div>';
        
        // JavaScript para vista previa en tiempo real
        ?>
        <script>
        document.getElementById('citas_humor_theme').addEventListener('change', function() {
            var theme = this.value;
            var preview = document.getElementById('theme-preview');
            
            // Hacer petición AJAX para obtener vista previa
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxurl);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    preview.innerHTML = xhr.responseText;
                }
            };
            xhr.send('action=citas_humor_preview&theme=' + theme + '&nonce=<?php echo wp_create_nonce("citas_humor_preview"); ?>');
        });
        </script>
        <?php
    }
    
    /**
     * Renderizar vista previa del tema
     */
    private function render_preview($theme) {
        $css = Citas_Humor_Styles::get_theme_css($theme);
        echo '<style>' . $css . '</style>';
        echo '<div class="cita-humor-box">';
        echo '<p class="cita-humor-texto">Esta es una vista previa del tema seleccionado. ¡Cada recarga mostrará una cita diferente!</p>';
        echo '</div>';
    }
    
    /**
     * Renderizar página de ajustes
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Mostrar mensaje si se guardó
        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'citas_humor_messages',
                'citas_humor_message',
                'Configuración guardada correctamente',
                'updated'
            );
        }
        
        settings_errors('citas_humor_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div style="background: #fff; padding: 20px; margin: 20px 0; border-left: 4px solid #2271b1;">
                <h2>Cómo usar el plugin</h2>
                <p>Para mostrar una cita en cualquier página o entrada, simplemente usa el shortcode:</p>
                <code style="background: #f0f0f0; padding: 5px 10px; display: inline-block;">[cita_humor]</code>
                <p style="margin-top: 10px;">También puedes personalizarlo con una clase CSS personalizada:</p>
                <code style="background: #f0f0f0; padding: 5px 10px; display: inline-block;">[cita_humor clase="mi-clase"]</code>
            </div>
            
            <form action="options.php" method="post">
                <?php
                settings_fields('citas_humor_settings');
                do_settings_sections('citas-humor-settings');
                submit_button('Guardar Configuración');
                ?>
            </form>
        </div>
        <?php
    }
}

// AJAX handler para vista previa
add_action('wp_ajax_citas_humor_preview', 'citas_humor_ajax_preview');
function citas_humor_ajax_preview() {
    check_ajax_referer('citas_humor_preview', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die();
    }
    
    $theme = isset($_POST['theme']) ? sanitize_text_field($_POST['theme']) : 'gradient';
    $css = Citas_Humor_Styles::get_theme_css($theme);
    
    echo '<style>' . $css . '</style>';
    echo '<div class="cita-humor-box">';
    echo '<p class="cita-humor-texto">Esta es una vista previa del tema seleccionado. ¡Cada recarga mostrará una cita diferente!</p>';
    echo '</div>';
    
    wp_die();
}
