<?php
session_start();

// ✅ CORS (permitir desde localhost y Vercel)
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Responder a preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ VALIDAR SESIÓN - CRÍTICO
if (!isset($_SESSION["sesion"]) || $_SESSION["sesion"] !== "OK") {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Incluir funciones
require('../include/link.php');

// Obtener parámetros
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$est = isset($_POST['est']) ? (int)$_POST['est'] : 0;

// Validar parámetros
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
$estados_permitidos = [1, 3, 5, 7, 9, 10, 12, 35, 37];
if (!in_array($est, $estados_permitidos)) {
    http_response_code(400);
    echo json_encode(['error' => 'Estado no permitido']);
    exit;
}

// Conectar a BD
if ($con = conectar()) {
    // ✅ SEGURO: Usar prepared statements o escapear correctamente
    $id_safe = (int)$id;
    $est_safe = (int)$est;
    
    // Actualizar estado
    $query = "UPDATE m3it3m SET status = $est_safe, horamodificado = NOW() WHERE idreg = $id_safe";
    
    if (sentencia($con, $query)) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar']);
    }
    
    desconectar($con);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión']);
}
?>