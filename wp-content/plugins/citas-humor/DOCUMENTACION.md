# Citas de Humor - Documentación

## Descripción

Plugin de WordPress que muestra citas humorísticas aleatorias mediante un shortcode. Incluye 5 temas visuales personalizables desde el panel de administración.

## Características

- 15 citas humorísticas originales y divertidas
- 5 temas visuales diferentes (Gradiente, Clásico, Minimalista, Oscuro, Retro)
- Panel de administración intuitivo con vista previa en tiempo real
- Diseño responsivo para todos los dispositivos
- Arquitectura modular y extensible
- Código limpio siguiendo estándares de WordPress

## Instalación

1. Copia la carpeta `citas-humor` al directorio `/wp-content/plugins/`
2. Ve a **Plugins** en el panel de WordPress
3. Activa **Citas de Humor**
4. Ve a **Ajustes → Citas de Humor** para configurar el tema

## Uso Básico

### Shortcode Simple

Agrega el siguiente shortcode en cualquier página, entrada o widget:

```
[cita_humor]
```

### Shortcode con Clase Personalizada

Puedes añadir una clase CSS personalizada:

```
[cita_humor clase="mi-clase-personalizada"]
```

### En Plantillas PHP

Para usar el shortcode directamente en archivos de tema:

```php
<?php echo do_shortcode('[cita_humor]'); ?>
```

### En Widgets

1. Ve a **Apariencia → Widgets**
2. Arrastra un widget de **Texto** o **HTML personalizado**
3. Agrega el shortcode `[cita_humor]`
4. Guarda

## Configuración

### Cambiar el Tema Visual

1. Ve a **Ajustes → Citas de Humor**
2. Selecciona un tema del menú desplegable
3. Observa la vista previa en tiempo real
4. Haz clic en **Guardar Configuración**

### Temas Disponibles

#### Gradiente Moderno (Predeterminado)

Diseño vibrante con gradiente morado y violeta, borde dorado.

#### Clásico

Estilo elegante con fondo gris claro, tipografía serif, borde azul.

#### Minimalista

Diseño limpio con bordes superiores e inferiores, sin fondo de color.

#### Oscuro

Tema oscuro con texto verde neón y efecto de resplandor.

#### Retro

Estilo vintage años 80 con gradiente rosa, morado y azul, tipografía monospace.

## Personalización Avanzada

### Agregar Nuevas Citas

Edita el archivo `includes/class-citas-data.php`:

```php
public static function get_citas() {
    return array(
        "Tu nueva cita aquí",
        "Otra cita divertida",
        // ... más citas
    );
}
```

### Crear un Nuevo Tema

Edita el archivo `includes/class-citas-styles.php`:

1. Agrega el tema a la lista en `get_themes()`:

```php
'mi_tema' => array(
    'name' => 'Mi Tema Personalizado',
    'description' => 'Descripción del tema'
)
```

2. Agrega los estilos CSS en `get_theme_css()`:

```php
'mi_tema' => "
    .cita-humor-box {
        background: #tu-color;
        padding: 25px 30px;
        // ... más estilos
    }
    .cita-humor-texto {
        color: #tu-color-texto;
        // ... más estilos
    }
"
```

### Sobrescribir Estilos con CSS Personalizado

En tu tema de WordPress, agrega CSS personalizado:

```css
.cita-humor-box {
  /* Tus estilos personalizados */
  background: #tucolor !important;
}

.cita-humor-texto {
  /* Tus estilos de texto */
  font-size: 20px !important;
}
```

## Ejemplos de Uso

### Ejemplo 1: En una Página

```
<h2>Cita del Día</h2>
[cita_humor]
<p>¿Te gustó? Recarga la página para ver otra.</p>
```

### Ejemplo 2: En Sidebar

Agrega un widget de HTML personalizado con:

```html
<div class="widget-citas">
  <h3>Humor del Día</h3>
  [cita_humor]
</div>
```

### Ejemplo 3: En Header del Tema

En `header.php`:

```php
<div class="site-header-quote">
    <?php echo do_shortcode('[cita_humor]'); ?>
</div>
```

### Ejemplo 4: Múltiples Citas

```
[cita_humor]
[cita_humor]
[cita_humor]
```

Cada instancia mostrará una cita diferente (aleatoria).

## Compatibilidad

- **WordPress:** 5.0 o superior
- **PHP:** 7.0 o superior
- **Editores:** Compatible con Gutenberg, Editor Clásico y constructores de páginas
- **Temas:** Compatible con cualquier tema de WordPress

## Solución de Problemas

### El shortcode no muestra nada

1. Verifica que el plugin esté activado
2. Revisa que no haya errores en **Herramientas → Salud del sitio**
3. Desactiva otros plugins temporalmente para detectar conflictos

### Los estilos no se aplican

1. Limpia la caché del navegador y del sitio
2. Verifica que no haya CSS personalizado sobrescribiendo los estilos
3. Cambia de tema en los ajustes del plugin

### Vista previa no funciona en admin

1. Verifica que JavaScript esté habilitado en tu navegador
2. Revisa la consola del navegador (F12) para errores
3. Prueba con otro navegador

## Preguntas Frecuentes

**¿Puedo usar el plugin en varios sitios?**  
Sí, es de código abierto bajo licencia GPL v2.

**¿Las citas son traducibles?**  
Actualmente están en español. Puedes editar las citas en el archivo de datos.

**¿Cuántas citas incluye?**  
15 citas originales, pero puedes agregar todas las que quieras.

**¿Puedo cambiar el tema solo en ciertas páginas?**  
No directamente, pero puedes crear un shortcode personalizado o usar CSS condicional.

**¿Afecta el rendimiento del sitio?**  
No, el plugin es muy ligero y no realiza consultas a bases de datos adicionales.

## Desinstalación

1. Desactiva el plugin desde **Plugins**
2. Haz clic en **Eliminar**
3. Las opciones guardadas se mantendrán. Para eliminarlas completamente, edita `wp_options` en la base de datos y elimina `citas_humor_theme`.

## Changelog

### Versión 2.0.0

- Refactorización completa con arquitectura modular
- Agregado panel de administración
- 5 temas visuales
- Vista previa en tiempo real
- Mejoras en la estructura de código

### Versión 1.0.0

- Lanzamiento inicial
- Shortcode básico
- Tema gradiente predeterminado
- 15 citas humorísticas

## Créditos

Desarrollado por WordPress AI  
Licencia: GPL v2 o superior

## Soporte

Para reportar bugs o solicitar funcionalidades, contacta al desarrollador.
