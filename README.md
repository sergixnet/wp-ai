# WordPress AI - Entorno de Desarrollo

Entorno de desarrollo local para WordPress con Docker, optimizado para desarrollo de temas y plugins.

## Estructura del Proyecto

```
wp-ai/
├── docker-compose.yml        # Configuración Docker
├── .env                       # Variables de entorno (no versionado)
├── .gitignore                # Archivos excluidos de Git
├── README.md                 # Este archivo
├── nginx/
│   ├── conf.d/
│   │   └── default.conf      # Configuración Nginx
│   └── ssl/                  # Certificados SSL (no versionados)
├── wordpress/                # Core WordPress (no versionado)
└── wp-content/               # TUS DESARROLLOS (versionados en Git)
    ├── plugins/              # Tus plugins personalizados
    │   └── citas-humor/     # Ejemplo: Plugin Citas de Humor
    └── themes/               # Tus temas personalizados
```

## Filosofía del Proyecto

Este entorno separa claramente:

- **Core de WordPress (`wordpress/`)**: No se versiona en Git, se instala automáticamente
- **Tu desarrollo (`wp-content/`)**: Plugins y temas propios, versionados en Git
- **Configuración (`docker-compose.yml`, `.env`)**: Entorno reproducible

## Instalación Inicial

### 1. Clonar el repositorio

```bash
git clone <tu-repo>
cd wp-ai
```

### 2. Configurar variables de entorno

Copia `.env.example` a `.env` (o créalo con estas variables):

```env
MYSQL_ROOT_PASSWORD=root_secure_password_2024
MYSQL_DATABASE=wordpress_db
MYSQL_USER=wordpress_user
MYSQL_PASSWORD=wordpress_secure_password_2024
```

### 3. Generar certificados SSL

```bash
mkdir -p nginx/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout nginx/ssl/key.pem \
  -out nginx/ssl/cert.pem \
  -subj "/C=ES/ST=State/L=City/O=Organization/CN=wp-ai.dev"
```

### 4. Agregar dominio al hosts

**En Linux/Mac:**

```bash
echo "127.0.0.1 wp-ai.dev" | sudo tee -a /etc/hosts
```

**En Windows (como Administrador en PowerShell):**

```powershell
Add-Content -Path C:\Windows\System32\drivers\etc\hosts -Value "`n127.0.0.1 wp-ai.dev"
```

### 5. Iniciar contenedores

```bash
docker compose up -d
```

### 6. Acceder a WordPress

- **Web:** https://wp-ai.dev
- **Admin:** https://wp-ai.dev/wp-admin

## Desarrollo de Plugins

### Crear un nuevo plugin

```bash
mkdir -p wp-content/plugins/mi-plugin
cd wp-content/plugins/mi-plugin
```

Crea el archivo principal `mi-plugin.php`:

```php
<?php
/**
 * Plugin Name: Mi Plugin
 * Description: Descripción de mi plugin
 * Version: 1.0.0
 */

// Tu código aquí
```

### Activar plugin con WP-CLI

```bash
docker exec wp-ai-wpcli wp plugin activate mi-plugin
```

### Plugin de ejemplo incluido

El proyecto incluye `citas-humor` como ejemplo de plugin modular con:

- Estructura organizada en clases
- Panel de administración
- Múltiples temas visuales
- Documentación completa

## Desarrollo de Temas

### Crear un nuevo tema

```bash
mkdir -p wp-content/themes/mi-tema
cd wp-content/themes/mi-tema
```

Crea `style.css`:

```css
/*
Theme Name: Mi Tema
Author: Tu Nombre
Version: 1.0.0
*/
```

Crea `index.php`:

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body>
    <h1><?php bloginfo('name'); ?></h1>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article>
            <h2><?php the_title(); ?></h2>
            <?php the_content(); ?>
        </article>
    <?php endwhile; endif; ?>
    <?php wp_footer(); ?>
</body>
</html>
```

### Activar tema con WP-CLI

```bash
docker exec wp-ai-wpcli wp theme activate mi-tema
```

## Comandos Útiles

### Docker

```bash
# Iniciar contenedores
docker compose up -d

# Ver logs en tiempo real
docker compose logs -f

# Detener contenedores
docker compose down

# Reiniciar servicios
docker compose restart
```

### WP-CLI

**Crear alias para simplificar comandos:**

```bash
alias wp="docker exec wp-ai-wpcli wp"
```

Ahora puedes usar `wp` directamente en lugar de `docker exec wp-ai-wpcli wp`:

```bash
# Sin alias
docker exec wp-ai-wpcli wp plugin list

# Con alias
wp plugin list
```

**Comandos frecuentes:**

```bash
# Plugins
wp plugin list
wp plugin activate mi-plugin
wp plugin deactivate mi-plugin

# Temas
wp theme list
wp theme activate mi-tema

# Cache
wp cache flush

# Información
wp core version
wp --info
```

### Git

```bash
# Agregar tus desarrollos
git add wp-content/plugins/mi-plugin
git add wp-content/themes/mi-tema

# Commit
git commit -m "feat: agregar mi plugin/tema"

# Push
git push origin main
```

## Workflow de Desarrollo

1. **Edita** archivos en `wp-content/plugins/` o `wp-content/themes/`
2. **Los cambios se reflejan** inmediatamente en Docker
3. **Versiona** tus cambios con Git
4. **Exporta** tu plugin/tema para producción

## Debug

Ya está activado `WP_DEBUG` en el entorno. Ver logs:

```bash
tail -f wordpress/wp-content/debug.log
```

## Base de Datos

### Acceso directo

```bash
docker exec -it wp-ai-db mysql -u wordpress_user -p
# Password: wordpress_secure_password_2024
```

### Cliente externo

- **Host:** localhost
- **Puerto:** 3306
- **Usuario:** wordpress_user
- **Contraseña:** wordpress_secure_password_2024
- **Base de datos:** wordpress_db

## Recursos

- [WordPress Developer Handbook](https://developer.wordpress.org/)
- [WP-CLI Documentation](https://wp-cli.org/)
- [Plugin Handbook](https://developer.wordpress.org/plugins/)
- [Theme Handbook](https://developer.wordpress.org/themes/)

## Licencia

Código abierto. Los plugins y temas que desarrolles tienen su propia licencia.
