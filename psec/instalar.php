<?php
// Quitamos psec/ porque la carpeta panel ya está aquí mismo
require 'panel/include/link.php'; 

$conn = conectar();

// Quitamos psec/ porque el archivo sql también está aquí mismo
$sql = file_get_contents('schema-mysql.sql');

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