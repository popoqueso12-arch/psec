<?php
/**
 * Crea tablas y usuario admin desde schema-mysql.sql (una sola vez en producción).
 *
 * Seguridad:
 * - En Render: define la variable de entorno PSE_INSTALL_KEY y abre:
 *   .../panel/install-schema.php?key=TU_CLAVE
 * - En local (XAMPP): sin PSE_INSTALL_KEY solo responde desde 127.0.0.1 / ::1
 *
 * Después de usar: borra este archivo o quita PSE_INSTALL_KEY del entorno.
 */
header('Content-Type: text/plain; charset=utf-8');

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$keyEnv = getenv('PSE_INSTALL_KEY');
$keyOk = ($keyEnv !== false && $keyEnv !== '' && isset($_GET['key']) && hash_equals((string) $keyEnv, (string) $_GET['key']));
$localOk = in_array($ip, ['127.0.0.1', '::1'], true);

if (!$keyOk && !($localOk && ($keyEnv === false || $keyEnv === ''))) {
    http_response_code(403);
    exit("Acceso denegado. En producción define PSE_INSTALL_KEY y usa ?key=...\n");
}

require_once __DIR__ . '/include/link.php';

$sqlFile = __DIR__ . '/schema-mysql.sql';
if (!is_readable($sqlFile)) {
    http_response_code(500);
    exit('No se encuentra schema-mysql.sql');
}

$sql = file_get_contents($sqlFile);
if ($sql === false || trim($sql) === '') {
    http_response_code(500);
    exit('schema-mysql.sql vacío');
}

$c = @conectar();
if (!$c) {
    http_response_code(500);
    exit('No hay conexión MySQL: ' . mysqli_connect_error());
}

mysqli_set_charset($c, 'utf8mb4');

if (!mysqli_multi_query($c, $sql)) {
    echo 'Error SQL: ' . mysqli_error($c) . "\n";
    desconectar($c);
    exit(1);
}

do {
    if ($r = mysqli_store_result($c)) {
        mysqli_free_result($r);
    }
} while (mysqli_next_result($c));

if (mysqli_errno($c)) {
    echo 'Error en lote: ' . mysqli_error($c) . "\n";
    desconectar($c);
    exit(1);
}

// Asegura usuario admin con contraseña adminÑ (UTF-8)
$u = mysqli_real_escape_string($c, 'admin');
$p = mysqli_real_escape_string($c, 'adminÑ');
$up = "INSERT INTO `m3us3r` (`usuario`, `password`) VALUES ('{$u}', '{$p}') "
    . "ON DUPLICATE KEY UPDATE `password` = VALUES(`password`)";
if (!mysqli_query($c, $up)) {
    echo 'Tablas OK. Aviso al fijar admin: ' . mysqli_error($c) . "\n";
} else {
    echo "Tablas creadas o ya existían.\n";
    echo "Usuario panel: admin\n";
    echo "Contraseña: adminÑ\n";
}

desconectar($c);
echo "\nListo. Borra install-schema.php cuando no lo necesites.\n";
