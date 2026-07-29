<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

// 1. Incluimos tu archivo de conexión (Ajusta la ruta según dónde pongas este archivo)
// Como la imagen muestra link.php en 'panel/include/', y este asumo va en 'panel/run/', subimos un nivel con '../'
include_once '../include/link.php'; 

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) { echo json_encode(['exito' => false]); exit; }

// 2. Nos conectamos usando tu función exacta
$conn = conectar();
if (!$conn) {
    echo json_encode(['exito' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

try {
    // Tomar IP del cliente
    $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    // Limpiamos el valor monetario
    $monto = preg_replace('/[^0-9]/', '', $data['monto']);

    // 3. Preparamos la consulta al estilo mysqli de tu proyecto
    $sql = "INSERT INTO m3it3m (nombre, apellido, tipo_doc, cedula, celular, email, direccion, referencia, empresa, agente, ip, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        // Enlazamos las variables ("s" significa que todos son strings)
        mysqli_stmt_bind_param($stmt, "sssssssssss", 
            $data['nombre'], $data['apellido'], $data['tipo_doc'], $data['cedula'], 
            $data['celular'], $data['correo'], $data['direccion'], 
            $data['referencia'], $data['empresa'], $monto, $ip
        );

        mysqli_stmt_execute($stmt);
        
        // Obtenemos el ID del registro recién creado usando tu conexión
        $idGenerado = mysqli_insert_id($conn);
        
        echo json_encode(['exito' => true, 'id' => $idGenerado]);
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['exito' => false, 'error' => mysqli_error($conn)]);
    }

} catch(Exception $e) {
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
} finally {
    // 4. Usamos tu función para desconectar
    desconectar($conn);
}
?>