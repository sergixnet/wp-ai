# WordPress AI - Docker Setup

Instalación de WordPress con Docker Compose, MySQL y Nginx con HTTPS.

## Requisitos Previos

- Docker y Docker Compose instalados
- Agregar `wp-ai.dev` a tu archivo `/etc/hosts`:
  ```bash
  sudo echo "127.0.0.1 wp-ai.dev" >> /etc/hosts
  ```

## Configuración Inicial

### 1. Generar Certificados SSL (auto-firmados para desarrollo)

```bash
mkdir -p nginx/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout nginx/ssl/key.pem \
  -out nginx/ssl/cert.pem \
  -subj "/C=ES/ST=State/L=City/O=Organization/CN=wp-ai.dev"
```

### 2. Configurar Variables de Entorno

El archivo `.env` ya está creado con credenciales por defecto. **Cámbialas en producción**.

### 3. Iniciar los Contenedores

```bash
docker-compose up -d
```

### 4. Acceder a WordPress

- Web: https://wp-ai.dev
- Base de datos: localhost:3306

**Credenciales de MySQL** (desde `.env`):

- Host: `localhost` (o `db` desde dentro de Docker)
- Puerto: `3306`
- Database: `wordpress_db`
- Usuario: `wordpress_user`
- Contraseña: `wordpress_secure_password_2024`

## WP-CLI

El proyecto incluye WP-CLI para gestionar WordPress desde la línea de comandos.

### Usar WP-CLI

```bash
# Ejecutar comandos directamente
docker exec wp-ai-wpcli wp plugin list
docker exec wp-ai-wpcli wp theme list
docker exec wp-ai-wpcli wp user list

# Crear un alias para facilitar el uso
alias wp="docker exec wp-ai-wpcli wp"

# Ahora puedes usar simplemente:
wp plugin list
wp theme list
wp --info
```

## Comandos Útiles

```bash
# Ver logs
docker compose logs -f

# Detener contenedores
docker compose down

# Detener y eliminar volúmenes (¡cuidado! elimina la BD)
docker compose down -v

# Reiniciar servicios
docker compose restart

# Acceder al contenedor de WordPress
docker exec -it wp-ai-wordpress bash

# Acceder a MySQL
docker exec -it wp-ai-db mysql -u wordpress_user -p
```

## Estructura del Proyecto

```
wp-ai/
├── docker-compose.yml       # Configuración de servicios Docker
├── .env                      # Variables de entorno (credenciales)
├── nginx/
│   ├── conf.d/
│   │   └── default.conf     # Configuración Nginx con SSL
│   └── ssl/
│       ├── cert.pem         # Certificado SSL
│       └── key.pem          # Clave privada SSL
└── wordpress/               # Archivos de WordPress (auto-generado)
```

## Notas

- El navegador mostrará una advertencia de certificado auto-firmado. Esto es normal en desarrollo.
- Los archivos de WordPress se montan en `./wordpress` para persistencia.
- La base de datos usa un volumen Docker para persistencia.
