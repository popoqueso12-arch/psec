<?php
// Ejecutar UNA vez para agregar la columna last_seen a m3it3m
require_once __DIR__ . '/include/link.php';
$con = conectar();
if (!$con) die('No DB');

// Verifica si la columna ya existe
$r = $con->query("SHOW COLUMNS FROM m3it3m LIKE 'last_seen'");
if ($r && $r->num_rows > 0) {
    echo "✅ Columna last_seen ya existe.";
} else {
    if ($con->query("ALTER TABLE m3it3m ADD COLUMN last_seen DATETIME DEFAULT NULL")) {
        echo "✅ Columna last_seen agregada correctamente.";
    } else {
        echo "❌ Error: " . $con->error;
    }
}
desconectar($con);
