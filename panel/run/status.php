<?php
session_start();

// ✅ 1. CONFIGURACIÓN CORS (Soporta Localhost y Vercel)
$allowed_origins = [
    'https://assasin-nine.vercel.app', 
    'http://localhost:5173'
];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: https://assasin-nine.vercel.app");
}

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Manejo de la petición de pre-vuelo (Preflight) de CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ 2. LECTURA DE DATOS (Soporta JSON de Axios/Fetch y FormData)
$usr = '';
$pas = '';
$id = 0;
$est = 0;

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (is_array($data) && (isset($data['usr']) || isset($data['pas']))) {
    $usr = isset($data['usr']) ? trim($data['usr']) : '';
    $pas = isset($data['pas']) ? trim($data['pas']) : '';
    $id = isset($data['id']) ? (int)$data['id'] : 0;
    $est = isset($data['est']) ? (int)$data['est'] : 0;
} else {
    $usr = isset($_POST['usr']) ? trim($_POST['usr']) : '';
    $pas = isset($_POST['pas']) ? trim($_POST['pas']) : '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $est = isset($_POST['est']) ? (int)$_POST['est'] : 0;
}

// Validar que existan las credenciales
if (empty($usr) || empty($pas)) {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales requeridas']);
    exit;
}

// ✅ 3. VALIDACIÓN DE PARÁMETROS DE ACTUALIZACIÓN
if (!$id || $id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

if (!$est || $est < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Estado inválido']);
    exit;
}

// Estados permitidos
$estados_permitidos = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 35, 37];
if (!in_array($est, $estados_permitidos)) {
    http_response_code(400);
    echo json_encode(['error' => 'Estado no permitido']);
    exit;
}

// ✅ 4. CONEXIÓN Y AUTENTICACIÓN SEGURA
require('../include/link.php');
$con = conectar();

if (!$con) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

// Prepared Statement para la autenticación
$stmt = $con->prepare("SELECT * FROM m3us3r WHERE usuario = ? AND password = ?");
$stmt->bind_param("ss", $usr, $pas);
$stmt->execute();
$resultado_auth = $stmt->get_result();

if ($resultado_auth->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales inválidas']);
    $stmt->close();
    desconectar($con);
    exit;
}
$stmt->close(); // Cerramos el statement de auth para proceder al UPDATE

// ✅ 5. ACTUALIZAR ESTADO
// Como $est e $id ya están forzados a enteros con (int), son seguros de usar directamente
$query = "UPDATE m3it3m SET status = $est, horamodificado = NOW() WHERE idreg = $id";

if (sentencia($con, $query)) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar']);
}

desconectar($con);
?>
