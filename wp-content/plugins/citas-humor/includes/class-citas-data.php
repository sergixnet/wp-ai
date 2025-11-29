<?php
/**
 * Clase para gestionar las citas
 */

if (!defined('ABSPATH')) {
    exit;
}

class Citas_Humor_Data {
    
    /**
     * Obtener todas las citas
     */
    public static function get_citas() {
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
     * Obtener una cita aleatoria
     */
    public static function get_random_cita() {
        $citas = self::get_citas();
        return $citas[array_rand($citas)];
    }
}
