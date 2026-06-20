<?php
// Le agregamos psec/ al inicio de la ruta
require 'psec/panel/include/link.php'; 

$conn = conectar();

// Le agregamos psec/ al nombre del archivo
$sql = file_get_contents('psec/schema-mysql.sql');

if (!$sql) {
    die("No se pudo leer el archivo schema-mysql.sql");
}

if (mysqli_multi_query($conn, $sql)) {
    echo "<h1>¡Éxito! Las tablas se crearon en Aiven.</h1>";
} else {
    echo "<h1>Hubo un error:</h1> " . mysqli_error($conn);
}

desconectar($conn);
?>