<?php
/**
 * Clase para gestionar las citas desde API externa (JokeAPI)
 */

if (!defined('ABSPATH')) {
    exit;
}

class Citas_Humor_Data {
    
    const API_URL = 'https://v2.jokeapi.dev/joke/Any';
    const CACHE_KEY_PREFIX = 'citas_humor_joke_';
    const CACHE_EXPIRATION = 300; // 5 minutos por chiste individual
    
    /**
     * Citas de respaldo en caso de fallo de API
     */
    private static function get_fallback_citas() {
        return array(
            "Mi dieta es muy estricta: solo como lo que cabe en mi boca. Por suerte, mi boca es muy flexible.",
            "He decidido ser productivo mañana. Hoy estoy ocupado posponiéndolo.",
            "Mi nivel de paciencia es directamente proporcional a la velocidad del Wi-Fi.",
            "Finalmente encontré la fórmula del éxito: café más siestas dividido entre más café.",
            "No soy perezoso, estoy en modo ahorro de energía para futuras aventuras épicas.",
            "Mi superpoder es convertir planes emocionantes en excusas creativas para quedarme en casa.",
            "El ejercicio es importante, por eso siempre salto a conclusiones y corro de mis responsabilidades.",
            "Mi memoria es excelente, solo que está archivada en un lugar que olvidé dónde está.",
            "Soy multilingüe: hablo español, sarcasmo y suspiros profundos con fluidez nativa.",
            "Mi relación con las plantas es complicada: ellas necesitan agua, yo necesito que me recuerden regarlas.",
            "No procrastino, simplemente doy prioridad a la relajación sobre la productividad. Es una estrategia de vida.",
            "Mi teléfono tiene más vida social que yo. Al menos él recibe notificaciones constantemente.",
            "Intento ser adulto responsable, pero alguien sigue poniendo galletas en el supermercado donde las veo.",
            "Mi cerebro tiene pestañas abiertas: tres están congeladas, dos hacen ruido y no sé dónde está la música.",
            "Dicen que la honestidad es lo mejor. Honestamente, prefiero una pizza que una ensalada cualquier día."
        );
    }
    
    /**
     * Obtener todas las citas (desde cache o fallback)
     */
    public static function get_citas() {
        return self::get_fallback_citas();
    }
    
    /**
     * Obtener una cita aleatoria desde la API
     */
    public static function get_random_cita() {
        // Verificar configuración de fuente
        $source = get_option('citas_humor_source', 'api');
        
        // Si está configurado para usar citas locales
        if ($source === 'local') {
            $citas = self::get_fallback_citas();
            return $citas[array_rand($citas)];
        }
        
        // Verificar si el cache está habilitado
        $use_cache = get_option('citas_humor_use_cache', '1') === '1';
        
        if ($use_cache) {
            // Intentar obtener del cache primero
            $cached_joke = self::get_cached_joke();
            if ($cached_joke !== false) {
                return $cached_joke;
            }
        }
        
        // Intentar obtener desde la API
        $joke = self::fetch_from_api();
        
        // Si falla, usar citas de respaldo
        if ($joke === false) {
            $citas = self::get_fallback_citas();
            return $citas[array_rand($citas)];
        }
        
        // Guardar en cache si está habilitado
        if ($use_cache) {
            self::save_joke_to_cache($joke);
        }
        
        return $joke;
    }
    
    /**
     * Obtener un chiste del cache
     */
    private static function get_cached_joke() {
        // Generar un número aleatorio para obtener uno de los chistes cacheados
        $random_index = rand(1, 10);
        $cache_key = self::CACHE_KEY_PREFIX . $random_index;
        
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            error_log('Citas Humor: Chiste obtenido del cache (key: ' . $cache_key . ')');
            return $cached;
        }
        
        return false;
    }
    
    /**
     * Guardar chiste en cache
     */
    private static function save_joke_to_cache($joke) {
        // Rotar entre 10 slots de cache para tener variedad
        $random_slot = rand(1, 10);
        $cache_key = self::CACHE_KEY_PREFIX . $random_slot;
        
        set_transient($cache_key, $joke, self::CACHE_EXPIRATION);
        error_log('Citas Humor: Chiste guardado en cache (key: ' . $cache_key . ', expira en ' . self::CACHE_EXPIRATION . 's)');
    }
    
    /**
     * Obtener chiste desde JokeAPI
     */
    private static function fetch_from_api() {
        // Configurar parámetros de la API
        $params = array(
            'lang' => 'es', // Español preferido
            'type' => 'single', // Solo chistes de una línea
            'safe-mode' => '', // Filtrar contenido inapropiado
        );
        
        $url = add_query_arg($params, self::API_URL);
        
        // Realizar petición HTTP
        $response = wp_remote_get($url, array(
            'timeout' => 5,
            'sslverify' => true,
        ));
        
        // Verificar errores de conexión
        if (is_wp_error($response)) {
            error_log('Citas Humor API Error: ' . $response->get_error_message());
            return false;
        }
        
        // Verificar código de respuesta
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            error_log('Citas Humor API Error: HTTP ' . $status_code);
            return false;
        }
        
        // Decodificar JSON
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        // Verificar estructura de respuesta
        if (!isset($data['joke']) && !isset($data['setup'])) {
            error_log('Citas Humor API Error: Invalid response structure');
            return false;
        }
        
        // Retornar chiste
        if (isset($data['joke'])) {
            return $data['joke'];
        } elseif (isset($data['setup']) && isset($data['delivery'])) {
            return $data['setup'] . ' ' . $data['delivery'];
        }
        
        return false;
    }
    
    /**
     * Limpiar cache manualmente
     */
    public static function clear_cache() {
        // Eliminar todos los slots de cache
        for ($i = 1; $i <= 10; $i++) {
            $cache_key = self::CACHE_KEY_PREFIX . $i;
            delete_transient($cache_key);
        }
        error_log('Citas Humor: Cache limpiado completamente (10 slots)');
    }
}
