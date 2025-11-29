# Bloque de Gutenberg - Citas de Humor

El plugin ahora incluye un bloque de Gutenberg además del shortcode tradicional.

## Desarrollo del Bloque

### Instalar dependencias

```bash
npm install
```

### Compilar (desarrollo)

```bash
npm run start
```

### Compilar (producción)

```bash
npm run build
```

## Estructura

```
src/
├── index.js        # Código del bloque
├── editor.css      # Estilos del editor
└── style.css       # Estilos del frontend

build/              # Archivos compilados (no versionar)
├── index.js
├── index.asset.php
├── editor.css
└── style.css
```

## Uso

1. En el editor de bloques, busca "Cita de Humor"
2. Inserta el bloque
3. Configura la clase CSS si lo deseas
4. Publica

El bloque mostrará automáticamente una cita aleatoria en el frontend.
