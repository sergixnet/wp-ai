# Citas de Humor - Documentación Técnica

## Arquitectura del Plugin

### Estructura de Archivos

```
citas-humor/
├── citas-humor.php                    # Archivo principal del plugin
├── admin/
│   └── class-citas-admin.php         # Clase de administración
├── includes/
│   ├── class-citas-data.php          # Gestión de datos (citas)
│   ├── class-citas-styles.php        # Gestión de estilos/temas
│   └── class-citas-shortcode.php     # Lógica del shortcode
├── DOCUMENTACION.md                   # Documentación de usuario
├── DOCUMENTACION-TECNICA.md          # Este archivo
└── readme.txt                         # Readme estándar de WordPress
```

## Componentes del Sistema

### 1. Archivo Principal (`citas-humor.php`)

**Propósito:** Punto de entrada del plugin. Define constantes, carga clases y gestiona hooks de activación/desactivación.

**Constantes Definidas:**

```php
CITAS_HUMOR_VERSION  // Versión actual del plugin
CITAS_HUMOR_PATH     // Ruta absoluta al directorio del plugin
CITAS_HUMOR_URL      // URL absoluta al directorio del plugin
```

**Hooks Principales:**

- `plugins_loaded` → Inicializa las clases
- `register_activation_hook` → Establece opciones por defecto
- `register_deactivation_hook` → Limpieza (si necesario)

**Flujo de Inicialización:**

```
1. Definir constantes
2. Cargar archivos de clases (require_once)
3. Hook 'plugins_loaded' → citas_humor_init()
4. Instanciar Citas_Humor_Shortcode
5. Si is_admin() → Instanciar Citas_Humor_Admin
```

### 2. Clase de Datos (`includes/class-citas-data.php`)

**Responsabilidad:** Gestión y almacenamiento de las citas.

**Métodos Públicos:**

```php
Citas_Humor_Data::get_citas()
// @return array - Retorna todas las citas disponibles
// Uso: $citas = Citas_Humor_Data::get_citas();

Citas_Humor_Data::get_random_cita()
// @return string - Retorna una cita aleatoria
// Uso: $cita = Citas_Humor_Data::get_random_cita();
```

**Estructura de Datos:**

```php
array(
    "Cita 1 con texto humorístico",
    "Cita 2 con texto humorístico",
    // ... más citas
)
```

**Extensibilidad:**
Para agregar citas desde base de datos u otra fuente:

```php
public static function get_citas() {
    // Opción 1: Desde opciones de WordPress
    $custom_citas = get_option('citas_humor_custom', array());

    // Opción 2: Desde post type personalizado
    $posts = get_posts(array('post_type' => 'cita'));

    // Opción 3: Desde archivo JSON
    $json = file_get_contents(CITAS_HUMOR_PATH . 'citas.json');
    $citas = json_decode($json, true);

    return array_merge(self::get_default_citas(), $custom_citas);
}
```

### 3. Clase de Estilos (`includes/class-citas-styles.php`)

**Responsabilidad:** Gestión de temas visuales y generación de CSS.

**Métodos Públicos:**

```php
Citas_Humor_Styles::get_themes()
// @return array - Array asociativo de temas disponibles
// Estructura: ['theme_key' => ['name' => '...', 'description' => '...']]

Citas_Humor_Styles::get_theme_css($theme)
// @param string $theme - Identificador del tema
// @return string - CSS completo para el tema
```

**Estructura de un Tema:**

```php
'theme_key' => array(
    'name' => 'Nombre del Tema',
    'description' => 'Descripción breve del tema'
)
```

**Estructura del CSS:**
Cada tema debe incluir estilos para:

- `.cita-humor-box` → Contenedor principal
- `.cita-humor-texto` → Elemento de texto

**CSS Base Común:**

```css
.cita-humor-texto::before {
  content: '"';
}
.cita-humor-texto::after {
  content: '"';
}
@media (max-width: 768px) {
  /* estilos móviles */
}
```

**Agregar Nuevo Tema:**

1. En `get_themes()`:

```php
'nuevo_tema' => array(
    'name' => 'Nuevo Tema',
    'description' => 'Descripción del nuevo tema'
)
```

2. En `get_theme_css()`:

```php
'nuevo_tema' => "
    .cita-humor-box {
        background: #color;
        /* ... más estilos ... */
    }
    .cita-humor-texto {
        color: #color;
        /* ... más estilos ... */
    }
"
```

### 4. Clase de Shortcode (`includes/class-citas-shortcode.php`)

**Responsabilidad:** Renderización del shortcode y carga de estilos en frontend.

**Constructor:**

```php
public function __construct() {
    add_shortcode('cita_humor', array($this, 'render_shortcode'));
    add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
}
```

**Métodos:**

```php
render_shortcode($atts)
// @param array $atts - Atributos del shortcode
// @return string - HTML renderizado
// Atributos soportados:
//   - clase: Clase CSS personalizada (default: 'cita-humor-box')

enqueue_styles()
// Carga el CSS del tema seleccionado
// Lee get_option('citas_humor_theme') para determinar qué tema usar
```

**Flujo de Renderización:**

```
1. Usuario agrega [cita_humor] en contenido
2. WordPress ejecuta render_shortcode()
3. Obtiene atributos (clase personalizada si existe)
4. Obtiene cita aleatoria desde Citas_Humor_Data
5. Genera HTML con cita escapada (esc_html)
6. Retorna HTML para inserción en contenido
```

**Output HTML:**

```html
<div class="cita-humor-box">
  <p class="cita-humor-texto">"Texto de la cita"</p>
</div>
```

### 5. Clase de Administración (`admin/class-citas-admin.php`)

**Responsabilidad:** Panel de configuración en el backend de WordPress.

**Constructor:**

```php
public function __construct() {
    add_action('admin_menu', array($this, 'add_menu_page'));
    add_action('admin_init', array($this, 'register_settings'));
}
```

**Estructura del Menú:**

- Ubicación: Ajustes → Citas de Humor
- Capacidad requerida: `manage_options`
- Slug: `citas-humor-settings`

**Settings API de WordPress:**

```php
register_setting('citas_humor_settings', 'citas_humor_theme')
// Grupo: citas_humor_settings
// Opción: citas_humor_theme (almacena tema seleccionado)

add_settings_section(...)
// ID: citas_humor_main_section
// Título: "Configuración de Temas"

add_settings_field(...)
// ID: citas_humor_theme_field
// Callback: render_theme_field()
```

**Vista Previa en Tiempo Real:**

JavaScript inline que escucha cambios en el selector:

```javascript
document
  .getElementById('citas_humor_theme')
  .addEventListener('change', function () {
    // AJAX request a 'citas_humor_preview'
    // Actualiza #theme-preview con nuevo HTML
  });
```

AJAX Handler:

```php
add_action('wp_ajax_citas_humor_preview', 'citas_humor_ajax_preview');
// Verifica nonce y capacidades
// Genera HTML de vista previa con tema seleccionado
```

## Almacenamiento de Datos

### Opciones de WordPress

```php
// Tema seleccionado
get_option('citas_humor_theme', 'gradient')
// Valor por defecto: 'gradient'
// Posibles valores: 'gradient', 'classic', 'minimal', 'dark', 'retro'
```

### Esquema de Base de Datos

No utiliza tablas personalizadas. Todo se guarda en `wp_options`:

```sql
SELECT * FROM wp_options WHERE option_name = 'citas_humor_theme';
```

## Hooks y Filtros

### Hooks de Acción Utilizados

```php
'plugins_loaded'        // Inicializar plugin
'admin_menu'            // Agregar página de ajustes
'admin_init'            // Registrar settings
'wp_enqueue_scripts'    // Cargar estilos frontend
'wp_ajax_citas_humor_preview'  // Vista previa AJAX
```

### Filtros Disponibles para Extensiones

**Futuras implementaciones sugeridas:**

```php
// Filtrar citas antes de seleccionar aleatoria
apply_filters('citas_humor_get_citas', $citas);

// Filtrar HTML del shortcode antes de renderizar
apply_filters('citas_humor_shortcode_output', $output, $cita, $atts);

// Filtrar CSS del tema antes de cargar
apply_filters('citas_humor_theme_css', $css, $theme);

// Filtrar temas disponibles
apply_filters('citas_humor_available_themes', $themes);
```

## Seguridad

### Sanitización y Validación

```php
// Atributos del shortcode
shortcode_atts()  // Valores por defecto seguros

// Output HTML
esc_attr()        // Escapar atributos HTML
esc_html()        // Escapar texto plano

// Opciones guardadas
sanitize_text_field()  // Limpiar input de usuario

// AJAX
check_ajax_referer()   // Verificar nonce
current_user_can()     // Verificar capacidades
```

### Prevención de Acceso Directo

Todos los archivos incluyen:

```php
if (!defined('ABSPATH')) {
    exit;
}
```

### Nonces en Formularios

```php
// Vista previa AJAX
wp_create_nonce('citas_humor_preview')
check_ajax_referer('citas_humor_preview', 'nonce')
```

## Rendimiento

### Optimizaciones

- **Sin consultas a BD adicionales:** Las citas están en memoria (array)
- **CSS inline:** Se carga una vez por página, no archivos externos
- **Sin JavaScript en frontend:** Solo HTML y CSS
- **Caché de WordPress:** Compatible con plugins de caché

### Consideraciones de Escalabilidad

Para grandes cantidades de citas:

```php
// Implementar caché de transients
$citas = get_transient('citas_humor_all');
if (false === $citas) {
    $citas = Citas_Humor_Data::get_citas();
    set_transient('citas_humor_all', $citas, HOUR_IN_SECONDS);
}
```

## Testing

### Tests Unitarios Sugeridos

```php
// Test obtención de citas
test_get_citas_returns_array()
test_get_random_cita_returns_string()

// Test temas
test_all_themes_have_css()
test_theme_css_contains_required_classes()

// Test shortcode
test_shortcode_renders_html()
test_shortcode_escapes_output()

// Test admin
test_settings_page_requires_capability()
test_theme_option_saves_correctly()
```

### Tests de Integración

```php
// Activación/desactivación
test_plugin_activation_sets_defaults()
test_plugin_deactivation_cleans_up()

// Compatibilidad
test_works_with_gutenberg()
test_works_with_classic_editor()
test_works_with_page_builders()
```

## Extensibilidad

### Crear un Addon

```php
/**
 * Plugin Name: Citas Humor - Addon Ejemplo
 * Description: Addon que agrega citas desde API externa
 */

add_filter('citas_humor_get_citas', function($citas) {
    $api_citas = fetch_from_api();
    return array_merge($citas, $api_citas);
});
```

### Registrar Tema Personalizado

```php
add_filter('citas_humor_available_themes', function($themes) {
    $themes['custom'] = array(
        'name' => 'Mi Tema',
        'description' => 'Tema personalizado'
    );
    return $themes;
});

add_filter('citas_humor_theme_css', function($css, $theme) {
    if ($theme === 'custom') {
        return "/* CSS personalizado */";
    }
    return $css;
}, 10, 2);
```

## Debugging

### Modo Debug

Agregar a `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Logs Personalizados

```php
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Citas Humor: ' . print_r($data, true));
}
```

### Herramientas Útiles

- Query Monitor - Plugin para debug de WordPress
- Debug Bar - Barra de debug en admin
- Browser DevTools - Inspeccionar HTML/CSS renderizado

## Convenciones de Código

### Estándares de WordPress

- **Naming:** Snake_case para clases, snake_case para funciones
- **Indentación:** 4 espacios
- **Comentarios:** PHPDoc para funciones y clases
- **Seguridad:** Sanitizar input, escapar output
- **Internacionalización:** Preparado para i18n (futuro)

### Prefijos

Todos los nombres usan prefijo `citas_humor_` o `Citas_Humor_` para evitar conflictos.

## Roadmap

### Versión 2.1.0

- [ ] Interfaz para agregar/editar citas desde admin
- [ ] Export/import de citas en JSON
- [ ] Widget nativo (además del shortcode)
- [ ] Bloque Gutenberg

### Versión 3.0.0

- [ ] Categorías de citas (humor, motivación, reflexión)
- [ ] Programación de citas (mostrar según fecha/hora)
- [ ] Integración con redes sociales
- [ ] API REST para acceso externo
- [ ] Internacionalización (i18n) multiidioma

## Referencias

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- [Settings API](https://developer.wordpress.org/plugins/settings/settings-api/)
- [Shortcode API](https://developer.wordpress.org/plugins/shortcodes/)
