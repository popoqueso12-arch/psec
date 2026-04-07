<?php
/**
 * Comprueba la conexión MySQL usando include/link.php
 * Abre en el navegador: .../pse/panel/db_test.php
 * Elimina este archivo cuando ya no lo necesites (no lo subas a producción).
 */
$esCli = (php_sapi_name() === 'cli');
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if (!$esCli && !in_array($ip, ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Solo desde localhost.');
}

header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/include/link.php';

echo "Probando conexión...\n";
$portTxt = $dbport !== null ? (string) $dbport : '(puerto por defecto)';
echo "Host: {$servername}:{$portTxt}\n";
echo "Base: {$database}\n\n";

$c = @conectar();
if (!$c) {
    echo "ERROR: no se obtuvo recurso mysqli.\n";
    exit(1);
}

$r = mysqli_query($c, 'SELECT 1 AS ok, DATABASE() AS db_actual, VERSION() AS version_mysql');
if (!$r) {
    echo 'ERROR en consulta: ' . mysqli_error($c) . "\n";
    desconectar($c);
    exit(1);
}

$row = mysqli_fetch_assoc($r);
echo "OK — conexión correcta.\n";
echo "- SELECT 1 → " . ($row['ok'] ?? '?') . "\n";
echo "- Base actual → " . ($row['db_actual'] ?? '(null)') . "\n";
echo "- MySQL → " . ($row['version_mysql'] ?? '?') . "\n";

desconectar($c);
echo "\nListo. Puedes borrar db_test.php si ya no lo usas.\n";
