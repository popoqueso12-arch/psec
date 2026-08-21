<?php
$ip = getenv("REMOTE_ADDR");
require_once __DIR__ . '/a/fecha_es.php';
$tiempo = b34f9_fecha_larga();

// Redirigir directamente a la sección de Personas
header("Location: a/login");
exit();
?>
