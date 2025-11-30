Eres un desarrollador experto en WordPress y Docker.

Crea una nueva instalación de WordPress usando docker compose.

Las credenciales para acceder a la base de datos u otras variables que necesites se guardarán en un archivo `.env`.

La base de datos sera accesible desde el puerto 3306 y la web desde `https://wp-ai.dev`

---

Actúa como un experto en desarrollo de plugins de WordPress y en generación de contenido creativo.
Tu tarea es crear un conjunto de citas humorísticas breves y originales (no copiadas de autores famosos)
que puedan mostrarse en un plugin de WordPress mediante un shortcode [cita_humor].

Requisitos:

- Cada cita debe tener entre 10 y 25 palabras.
- El tono debe ser ligero, divertido y apto para todo público.
- Genera al menos 15 citas diferentes.
- Devuelve el resultado en formato JSON con la siguiente estructura:

{
"citas": [
{"texto": "Ejemplo de cita humorística 1"},
{"texto": "Ejemplo de cita humorística 2"},
...
]
}

Objetivo: El JSON será consumido por un plugin de WordPress que mostrará una cita aleatoria
en cada carga de página. Asegúrate de que las frases sean variadas y creativas.

---

## Plan: Sistema de Reservas para Restaurante en WordPress

Plugin completo para gestionar reservas de mesas con calendario, panel de administración para mesas y reservas, y formulario frontend para clientes. Usará tablas personalizadas de MySQL para eficiencia, siguiendo los patrones establecidos en el plugin citas-humor.

### Steps

1. **Crear estructura del plugin** con archivo principal `restaurant-reservations.php` que define constantes, versión, hooks de activación/desactivación para crear tablas `wp_rr_tables` (id, name, capacity, status) y `wp_rr_reservations` (id, table_id, customer_name, customer_email, customer_phone, reservation_date, reservation_time, party_size, status, created_at)

2. **Implementar gestión de mesas** en `admin/class-reservations-tables-admin.php` con página de administración para CRUD de mesas (listar, añadir, editar, eliminar), usando `WP_List_Table` para la interfaz y clase `includes/class-reservations-tables-data.php` para operaciones en base de datos con métodos `create_table()`, `update_table()`, `delete_table()`, `get_all_tables()`

3. **Crear sistema de calendario** en `admin/class-reservations-calendar-admin.php` con vista mensual/semanal/diaria de reservas, filtros por mesa, y clase `includes/class-reservations-calendar.php` para lógica de disponibilidad, verificación de conflictos horarios, y cálculo de franjas disponibles

4. **Desarrollar gestión de reservas** en `admin/class-reservations-admin.php` con lista de todas las reservas, filtros por estado (pendiente/confirmada/cancelada/completada), acciones de confirmación/cancelación, y clase `includes/class-reservations-data.php` con métodos `create_reservation()`, `update_status()`, `get_reservations_by_date()`, validación de disponibilidad

5. **Implementar formulario frontend** en `includes/class-reservations-shortcode.php` con shortcode `[restaurant_reservations]`, selector de fecha/hora/número de personas, verificación de disponibilidad en tiempo real vía AJAX en `assets/js/reservations.js`, y sistema de notificaciones por email en `includes/class-reservations-email.php` para confirmaciones

6. **Añadir configuración y ajustes** en `admin/class-reservations-settings.php` para horarios de apertura/cierre, duración de reservas por defecto (ej: 2 horas), días cerrados, tiempo mínimo de antelación, capacidad máxima simultánea, y plantillas de emails, usando WordPress Settings API con secciones organizadas

### Further Considerations

1. **¿Incluir sistema de confirmación por cliente?** Opción A: Email con enlace de confirmación (más seguro), Opción B: Confirmación automática (más simple), Opción C: Confirmación manual solo por admin
2. **¿Soporte para Gutenberg block además del shortcode?** Bloque visual para seleccionar mesa y fecha facilitaría inserción en páginas
3. **¿Gestión de turnos o franjas horarias?** Definir intervalos fijos (12:00, 14:00, 20:00, 22:00) vs permitir cualquier horario dentro del rango de apertura
4. **¿Dashboard con estadísticas?** Métricas de ocupación, reservas por mes, mesas más solicitadas, tasa de cancelación
