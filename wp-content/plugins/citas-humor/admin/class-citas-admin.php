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
        register_setting('citas_humor_settings', 'citas_humor_theme', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'gradient'
        ));
        
        register_setting('citas_humor_settings', 'citas_humor_source', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'api'
        ));
        
        register_setting('citas_humor_settings', 'citas_humor_use_cache', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        ));
        
        add_settings_section(
            'citas_humor_main_section',
            'Configuración de Temas',
            array($this, 'render_section_description'),
            'citas-humor-settings'
        );
        
        add_settings_section(
            'citas_humor_source_section',
            'Configuración de Fuente de Datos',
            array($this, 'render_source_section_description'),
            'citas-humor-settings'
        );
        
        add_settings_field(
            'citas_humor_theme_field',
            'Seleccionar Tema',
            array($this, 'render_theme_field'),
            'citas-humor-settings',
            'citas_humor_main_section'
        );
        
        add_settings_field(
            'citas_humor_source_field',
            'Fuente de Citas',
            array($this, 'render_source_field'),
            'citas-humor-settings',
            'citas_humor_source_section'
        );
        
        add_settings_field(
            'citas_humor_cache_field',
            'Usar Cache',
            array($this, 'render_cache_toggle_field'),
            'citas-humor-settings',
            'citas_humor_source_section'
        );
    }
    
    /**
     * Descripción de la sección de fuente
     */
    public function render_source_section_description() {
        echo '<p>Configura de dónde obtener las citas humorísticas.</p>';
    }
    
    /**
     * Renderizar campo de fuente de datos
     */
    public function render_source_field() {
        $current_source = get_option('citas_humor_source', 'api');
        ?>
        <select name="citas_humor_source" id="citas_humor_source">
            <option value="api" <?php selected($current_source, 'api'); ?>>API Externa (JokeAPI)</option>
            <option value="local" <?php selected($current_source, 'local'); ?>>Citas Locales</option>
        </select>
        <p class="description">
            <strong>API Externa:</strong> Obtiene chistes frescos desde JokeAPI (requiere conexión a internet).<br>
            <strong>Citas Locales:</strong> Usa las 15 citas predefinidas en el plugin (siempre disponibles).
        </p>
        <?php
    }
    
    /**
     * Renderizar campo de toggle de cache
     */
    public function render_cache_toggle_field() {
        $use_cache = get_option('citas_humor_use_cache', '1');
        ?>
        <label>
            <input type="checkbox" name="citas_humor_use_cache" value="1" <?php checked($use_cache, '1'); ?>>
            Activar sistema de cache
        </label>
        <p class="description">
            <strong>Activado:</strong> Los chistes se guardan en cache durante 5 minutos, reduciendo llamadas a la API y mejorando el rendimiento.<br>
            <strong>Desactivado:</strong> Cada visita obtiene un chiste nuevo directamente de la API (mayor variedad pero más lento).<br>
            <em>Nota: El sistema usa 10 slots de cache rotativos para mantener variedad en los chistes.</em>
        </p>
        <?php
    }
    
    /**
     * Renderizar campo de gestión de cache
     */
    public function render_cache_field() {
        ?>
        <p class="description">
            El cache almacena chistes de la API para mejorar el rendimiento.<br>
            <a href="<?php echo wp_nonce_url(admin_url('options-general.php?page=citas-humor-settings&action=clear_cache'), 'clear_cache', 'cache_nonce'); ?>" class="button button-secondary">
                Limpiar Cache
            </a>
        </p>
        <?php
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
        
        // Manejar limpieza de cache
        if (isset($_GET['action']) && $_GET['action'] === 'clear_cache' && isset($_GET['cache_nonce'])) {
            if (wp_verify_nonce($_GET['cache_nonce'], 'clear_cache')) {
                Citas_Humor_Data::clear_cache();
                add_settings_error(
                    'citas_humor_messages',
                    'citas_humor_cache_cleared',
                    'Cache limpiado correctamente',
                    'updated'
                );
            }
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
            
            <div style="background: #fff; padding: 20px; margin: 20px 0; border-left: 4px solid #00a32a;">
                <h2>Gestión de Cache</h2>
                <p class="description">
                    El cache almacena chistes de la API para mejorar el rendimiento.<br><br>
                    <a href="<?php echo wp_nonce_url(admin_url('options-general.php?page=citas-humor-settings&action=clear_cache'), 'clear_cache', 'cache_nonce'); ?>" class="button button-secondary">
                        Limpiar Cache
                    </a>
                </p>
            </div>
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
