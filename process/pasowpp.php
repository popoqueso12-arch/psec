<?php
require('../panel/include/setings.php');

if (empty($_COOKIE['id'])) {
	return;
}
$reg = (string) $_COOKIE['id'];
if ($reg === '' || !ctype_digit($reg)) {
	return;
}

// Actualizar el estado a 14 (esperando respuesta WhatsApp)
put_wpp($reg);

echo "OK";
?>