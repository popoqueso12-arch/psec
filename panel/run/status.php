<?php
session_start();

// ✅ CORS
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ VALIDAR CREDENCIALES (POST)
$usr = isset($_POST['usr']) ? trim($_POST['usr']) : '';
$pas = isset($_POST['pas']) ? trim($_POST['pas']) : '';

if (empty($usr) || empty($pas)) {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales requeridas']);
    exit;
}

// Validar credenciales en BD
require('../include/link.php');

if ($con = conectar()) {
    $usr_safe = $con->real_escape_string($usr);
    $pas_safe = $con->real_escape_string($pas);
    
    $consulta = sentencia($con, "SELECT * FROM m3us3r WHERE usuario = '" . $usr_safe . "' AND password = '" . $pas_safe . "'");
    
    if (!contarfilas($consulta)) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales inválidas']);
        desconectar($con);
        exit;
    }
    
    // ✅ CREDENCIALES VÁLIDAS - Obtener parámetros
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $est = isset($_POST['est']) ? (int)$_POST['est'] : 0;

    // Validar parámetros
    if (!$id || $id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID inválido']);
        desconectar($con);
        exit;
    }

    if (!$est || $est < 1) {
        http_response_code(400);
        echo json_encode(['error' => 'Estado inválido']);
        desconectar($con);
        exit;
    }

    // Estados permitidos
    $estados_permitidos = [1, 3, 5, 7, 9, 10, 12, 35, 37];
    if (!in_array($est, $estados_permitidos)) {
        http_response_code(400);
        echo json_encode(['error' => 'Estado no permitido']);
        desconectar($con);
        exit;
    }

    // ✅ Actualizar estado
    $query = "UPDATE m3it3m SET status = $est, horamodificado = NOW() WHERE idreg = $id";
    
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