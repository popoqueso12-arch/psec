<?php
/**
 * RATE LIMITING - Protege contra spam/brute force
 */

function checkRateLimit($ip, $limit = 3, $window = 900, $block_time = 900) {
    // Ruta para almacenar rate limit data
    $rate_limit_dir = sys_get_temp_dir() . '/psec_rate_limit';
    
    // Crear directorio si no existe
    if (!is_dir($rate_limit_dir)) {
        @mkdir($rate_limit_dir, 0777, true);
    }
    
    // Archivo para esta IP
    $ip_file = $rate_limit_dir . '/' . md5($ip) . '.json';
    
    $now = time();
    $data = ['attempts' => [], 'blocked_until' => 0];
    
    // Leer datos previos
    if (file_exists($ip_file)) {
        $content = file_get_contents($ip_file);
        $data = json_decode($content, true) ?? $data;
    }
    
    // ❌ BLOQUEADO?
    if ($data['blocked_until'] > $now) {
        $remaining = ceil(($data['blocked_until'] - $now) / 60);
        http_response_code(429); // Too Many Requests
        echo json_encode([
            'status' => 'ERROR',
            'message' => "IP bloqueada por spam. Intenta en $remaining minuto(s)",
            'blocked_until' => $data['blocked_until']
        ]);
        exit;
    }
    
    // Limpiar intentos viejos (fuera de la ventana)
    $data['attempts'] = array_filter($data['attempts'], function($timestamp) use ($now, $window) {
        return ($now - $timestamp) < $window;
    });
    
    // ¿EXCEDIÓ EL LÍMITE?
    if (count($data['attempts']) >= $limit) {
        // Bloquear por 15 minutos
        $data['blocked_until'] = $now + $block_time;
        file_put_contents($ip_file, json_encode($data));
        
        http_response_code(429); // Too Many Requests
        echo json_encode([
            'status' => 'ERROR',
            'message' => 'Demasiadas solicitudes. IP bloqueada por 15 minutos.',
            'blocked_until' => $data['blocked_until']
        ]);
        exit;
    }
    
    // Agregar este intento
    $data['attempts'][] = $now;
    $data['blocked_until'] = 0; // Desbloquear si estaba marcado
    file_put_contents($ip_file, json_encode($data));
    
    // ✅ PERMITIDO
    return true;
}

// Obtener IP real (funciona en Render y con proxies)
function getClientIP() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
        return $_SERVER['HTTP_X_FORWARDED'];
    } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
        return $_SERVER['HTTP_FORWARDED'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

// Función extra para tu modo DEV (resetear límite)
function resetRateLimit($ip) {
    $rate_limit_dir = sys_get_temp_dir() . '/psec_rate_limit';
    $ip_file = $rate_limit_dir . '/' . md5($ip) . '.json';
    if (file_exists($ip_file)) {
        unlink($ip_file);
    }
}
?>