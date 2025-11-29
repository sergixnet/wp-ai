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
